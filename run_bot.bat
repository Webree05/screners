@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"
title AI TRADING BOT - SUPREME RESILIENCE MODE
color 0B

:INIT
cls
echo ====================================================
echo    AI STOCK SCREENER - SUPREME BACKGROUND SERVICE
echo    Status: Always-On Multi-Engine Loop Activated
echo    Version: 5.5 (Auto-Heal Enabled)
echo ====================================================
echo.

:CHECK_PYTHON
echo [%TIME%] Checking AI Smart Engine Status...
netstat -ano | findstr :8000 > nul
if %errorlevel% neq 0 (
    echo [%TIME%] AI Smart Engine is DOWN. Launching Neural Core...
    start /b python -m uvicorn backend.main:app --host 0.0.0.0 --port 8000 --no-access-log > nul 2>&1
    timeout /t 5 > nul
) else (
    echo [%TIME%] AI Smart Engine is ACTIVE.
)

:RUN_SCRAPER
echo [%TIME%] Executing Intelligent Scraper Cycle...
echo ----------------------------------------------------
"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" backend\scraper.php
echo ----------------------------------------------------

echo.
echo [%TIME%] Cycle Completed. 
echo [%TIME%] Bot will standby for 10 seconds before next deep-scan...
timeout /t 10 /nobreak

echo.
echo [%TIME%] Self-Diagnostic in progress...
goto CHECK_PYTHON