# LPR microservice (YOLO + EasyOCR)

Uses your **laptop webcam** via the Laravel guard page.

## Python version (important)

You need **Python 3.8+** ( **3.10 or 3.11 recommended** ).  
If `pip` only shows FastAPI up to `0.83`, your `python` command is **too old** (often 3.7).

```powershell
python --version
```

### Windows — use `py -3.13` (you have 3.13 / 3.14 installed)

**Do not** run plain `python -m venv` — that often picks Python 3.7 and fails on numpy/torch.

```powershell
cd D:\laragon\www\SmartParkingManagementSystem\lpr-service

Remove-Item -Recurse -Force .venv -ErrorAction SilentlyContinue

py -3.13 -m venv .venv
.\.venv\Scripts\Activate.ps1
python --version
# MUST show 3.13.x (not 3.7!)

python -m pip install --upgrade pip
pip install -r requirements.txt
```

Or run the helper:

```powershell
.\setup.ps1
```

List installed Pythons: `py -0p`

## Run

```powershell
python -m uvicorn main:app --host 127.0.0.1 --port 8787
```

Open health check: http://127.0.0.1:8787/health

Laravel `.env`: `LPR_SERVICE_URL=http://127.0.0.1:8787`

## Optional: plate-only YOLO weights

```powershell
$env:LPR_PLATE_MODEL="D:\models\plate_best.pt"
python -m uvicorn main:app --host 127.0.0.1 --port 8787
```

## Python 3.7 only (not recommended)

```powershell
pip install -r requirements-py37.txt
```

Runs **EasyOCR only** (no YOLO). Upgrade to 3.10+ for full pipeline.

## Tips

- Good lighting and aim the webcam at the plate.
- First run downloads YOLOv8n and EasyOCR models (~100MB+).
