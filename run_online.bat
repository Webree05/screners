@echo off
TITLE DEX AI - CLOUD SERVER MODE
COLOR 0B

echo ========================================================
echo         DEX AI SMART SCREENER - CLOUD MODE
echo ========================================================
echo.

:: Check if cloudflared exists
if not exist "cloudflared.exe" (
    echo [ERROR] cloudflared.exe tidak ditemukan!
    echo Silakan download dan letakkan di folder ini.
    pause
    exit
)

:: Start the Python Backend in a new window
echo [SYSTEM] Starting Neural Engine (Port 8000)...
start /B python -m uvicorn backend.main:app --host 0.0.0.0 --port 8000

:: Start the PHP Scraper in a new window
echo [SYSTEM] Starting Data Scraper loop...
start /B php backend/scraper.php

echo.
echo [CLOUD] Menyiapkan Terowongan Cloudflare...
echo [NOTICE] Tunggu sampai muncul link "https://...trycloudflare.com"
echo.

:: Start Cloudflare Tunnel
.\cloudflared.exe tunnel --url http://localhost:8000

pause
