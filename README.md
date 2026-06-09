# Smart Parking Management System

Laravel 13 web application implementing the core SRS modules: user management (RBAC), zones & slots, reservations with QR codes, guard verification (QR + license plate), billing/payments, and admin reporting.

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- MySQL (recommended with Laragon) or SQLite with PDO enabled

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure database in `.env`, then:

```bash
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

Optional: run the scheduler to expire pending bookings:

```bash
php artisan schedule:work
```

## Demo accounts

| Role   | Email               | Password  |
|--------|---------------------|-----------|
| Admin  | admin@parking.test  | password  |
| Guard  | guard@parking.test  | password  |
| Driver | driver@parking.test | password  |

## Features

- **Driver:** search & book slots, QR for gate entry, vehicles, profile, pay & print invoice
- **Guard:** slot monitor, verify QR or license plate at entry/exit, **webcam LPR**, activity logs
- **Admin:** zones, slots, users/roles, bookings, revenue export, settings (rates), logs

## Laptop webcam LPR (YOLO + EasyOCR)

1. Create a Python venv and install dependencies — see **`lpr-service/README.md`**.
2. Start the service on **127.0.0.1:8787**:

   ```bash
   cd lpr-service
   pip install -r requirements.txt
   python -m uvicorn main:app --host 127.0.0.1 --port 8787
   ```

3. Optional: add `LPR_SERVICE_URL=http://127.0.0.1:8787` to `.env`.
4. Sign in as **Guard** → **Camera LPR (YOLO)**.

Pipeline: **Ultralytics YOLOv8n** (COCO vehicle boxes) → lower-center crop → **EasyOCR**. You can optionally set **`LPR_PLATE_MODEL`** to a YOLO `.pt` that detects licence plates directly.

## License

MIT — academic / project use.
