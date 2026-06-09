"""
Laptop webcam → Laravel → here.

Pipeline:
1) Optional: YOLO weights at LPR_PLATE_MODEL detect license-plate bounding boxes → EasyOCR on crops.
2) Else: Ultralytics YOLOv8n (COCO) finds car/bus/truck/motorcycle → lower-center ROI → EasyOCR.
3) Fallback: EasyOCR on downscaled full frame.

Install: pip install -r requirements.txt
Run: uvicorn main:app --host 127.0.0.1 --port 8787
"""

from __future__ import annotations

import logging
import os
from typing import Any

import cv2
import numpy as np
from fastapi import FastAPI, File, HTTPException, UploadFile

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("lpr")

app = FastAPI(title="Smart Parking LPR", version="1.0")

_reader = None
_yolo_vehicle = None
_yolo_plate = None

# COCO: car=2 motorcycle=3 bus=5 truck=7
VEHICLE_CLASS_IDS = {2, 3, 5, 7}


def get_reader():
    global _reader
    if _reader is None:
        import easyocr

        _reader = easyocr.Reader(["en"], gpu=False, verbose=False)
    return _reader


def get_vehicle_model():
    global _yolo_vehicle
    if _yolo_vehicle is None:
        try:
            from ultralytics import YOLO

            _yolo_vehicle = YOLO("yolov8n.pt")
        except ImportError:
            logger.warning("ultralytics not installed — using EasyOCR-only mode (install Python 3.10+ and requirements.txt)")
            _yolo_vehicle = False
    return _yolo_vehicle if _yolo_vehicle is not False else None


def get_plate_model():
    """Optional plate-specific YOLO (set env LPR_PLATE_MODEL=/path/to/best.pt)."""
    global _yolo_plate
    path = os.getenv("LPR_PLATE_MODEL", "").strip()
    if not path or not os.path.isfile(path):
        return None
    if _yolo_plate is None:
        try:
            from ultralytics import YOLO

            _yolo_plate = YOLO(path)
        except ImportError:
            logger.warning("ultralytics not installed — cannot load LPR_PLATE_MODEL")
            _yolo_plate = False
    return _yolo_plate if _yolo_plate is not False else None


def plate_roi_from_vehicle(x1: int, y1: int, x2: int, y2: int) -> tuple[int, int, int, int]:
    h = max(1, y2 - y1)
    w = max(1, x2 - x1)
    ya = int(y1 + 0.52 * h)
    yb = int(y2)
    xa = int(x1 + 0.12 * w)
    xb = int(x2 - 0.12 * w)
    return xa, ya, xb, yb


def ocr_plate(image_bgr: np.ndarray) -> list[dict[str, Any]]:
    reader = get_reader()
    gray = cv2.cvtColor(image_bgr, cv2.COLOR_BGR2GRAY)
    gray = cv2.bilateralFilter(gray, 11, 41, 41)
    results = reader.readtext(gray, detail=1, paragraph=False, allowlist="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ")
    out: list[dict[str, Any]] = []
    for _bbox, text, conf in results:
        t = "".join(c for c in text.upper().replace(" ", "") if c.isalnum())
        if len(t) >= 4 and float(conf) >= 0.12:
            out.append({"text": t, "confidence": float(conf), "raw": text})
    return out


def process_image(buf: bytes) -> dict[str, Any]:
    arr = np.asarray(bytearray(buf), dtype=np.uint8)
    frame = cv2.imdecode(arr, cv2.IMREAD_COLOR)
    if frame is None:
        raise ValueError("Could not decode image")

    h, w = frame.shape[:2]
    candidates: list[dict[str, Any]] = []

    plate_model = get_plate_model()
    if plate_model is not None:
        det = plate_model.predict(source=frame, verbose=False)[0]
        for b in det.boxes:
            xyxy = b.xyxy[0].tolist()
            x1, y1, x2, y2 = int(xyxy[0]), int(xyxy[1]), int(xyxy[2]), int(xyxy[3])
            x1, y1 = max(0, x1), max(0, y1)
            x2, y2 = min(w, x2), min(h, y2)
            crop = frame[y1:y2, x1:x2]
            if crop.size == 0:
                continue
            for o in ocr_plate(crop):
                o["source"] = "plate_yolo"
                candidates.append(o)
    elif get_vehicle_model() is not None:
        model = get_vehicle_model()
        det = model.predict(source=frame, verbose=False)[0]
        for b in det.boxes:
            cls_id = int(b.cls[0])
            if cls_id not in VEHICLE_CLASS_IDS:
                continue
            xyxy = b.xyxy[0].tolist()
            x1, y1, x2, y2 = int(xyxy[0]), int(xyxy[1]), int(xyxy[2]), int(xyxy[3])
            x1, y1 = max(0, x1), max(0, y1)
            x2, y2 = min(w, x2), min(h, y2)
            xa, ya, xb, yb = plate_roi_from_vehicle(x1, y1, x2, y2)
            xa, ya = max(0, xa), max(0, ya)
            xb, yb = min(w, xb), min(h, yb)
            crop = frame[ya:yb, xa:xb]
            if crop.size == 0:
                continue
            for o in ocr_plate(crop):
                o["source"] = "vehicle_yolo_roi"
                candidates.append(o)

    if not candidates:
        scaled = frame
        if w > 1280:
            scale = 1280 / w
            scaled = cv2.resize(frame, (1280, int(h * scale)))
        for o in ocr_plate(scaled):
            o["source"] = "easyocr_fullframe"
            candidates.append(o)

    best_by_text: dict[str, dict[str, Any]] = {}
    for c in candidates:
        key = c["text"]
        if key not in best_by_text or c["confidence"] > best_by_text[key]["confidence"]:
            best_by_text[key] = c

    sorted_plates = sorted(best_by_text.values(), key=lambda x: -x["confidence"])
    primary = sorted_plates[0]["text"] if sorted_plates else ""

    return {
        "success": True,
        "primary_plate": primary,
        "candidates": sorted_plates,
        "pipeline": ("plate_yolo" if plate_model else "vehicle_yolo_easyocr"),
    }


@app.post("/recognize")
async def recognize(image: UploadFile = File(...)):
    if not image.content_type or not image.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="Expected an image file")
    buf = await image.read()
    if len(buf) > 8 * 1024 * 1024:
        raise HTTPException(status_code=400, detail="Image too large (max 8MB)")
    try:
        return process_image(buf)
    except HTTPException:
        raise
    except Exception as e:
        logger.exception("recognize failed")
        raise HTTPException(status_code=500, detail=str(e)) from e


@app.get("/health")
def health():
    return {"status": "ok"}
