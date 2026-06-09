# Creates .venv with Python 3.13+ (NOT the old default "python" on PATH)
$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

function Find-Python {
    foreach ($ver in @("3.13", "3.12", "3.11", "3.14")) {
        & py "-$ver" -c "import sys; print(sys.version)" 2>$null
        if ($LASTEXITCODE -eq 0) { return $ver }
    }
    return $null
}

$pyVer = Find-Python
if (-not $pyVer) {
    Write-Host "No Python 3.11+ found. Install from https://www.python.org/downloads/ or: py install 3.13" -ForegroundColor Red
    exit 1
}

Write-Host "Using: py -$pyVer" -ForegroundColor Cyan

if (Test-Path .venv) {
    Write-Host "Removing old .venv ..."
    Remove-Item -Recurse -Force .venv
}

& py "-$pyVer" -m venv .venv
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

.\.venv\Scripts\Activate.ps1

Write-Host "Venv Python:" -ForegroundColor Yellow
python --version

$v = python -c "import sys; print(sys.version_info[:2])"
$major, $minor = python -c "import sys; print(sys.version_info[0], sys.version_info[1])"
if ([int]$major -lt 3 -or ([int]$major -eq 3 -and [int]$minor -lt 10)) {
    Write-Host "ERROR: venv is still too old. Do not use 'python -m venv' without py -3.13" -ForegroundColor Red
    exit 1
}

python -m pip install --upgrade pip
pip install -r requirements.txt

Write-Host "`nSuccess. Start service with:" -ForegroundColor Green
Write-Host "  .\.venv\Scripts\Activate.ps1"
Write-Host "  python -m uvicorn main:app --host 127.0.0.1 --port 8787"
