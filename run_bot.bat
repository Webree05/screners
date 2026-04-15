@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"
title AI TRADING BOT - SUPREME RESILIENCE MODE
color 0B

:INIT
cls
echo ====================================================
echo ====================================================
echo    AI STOCK SCREENER - STANDALONE INTEGRATED ENGINE
echo    ----------------------------------------------------
echo    NEW DASHBOARD URL: http://localhost:8000
echo    ----------------------------------------------------
echo    Status: Always-On Standalone Server Activated
echo    Notice: Laragon/Apache is NO LONGER REQUIRED.
echo ====================================================
echo.

:CHECK_PYTHON
echo [%TIME%] Checking AI Standalone Server (Port 8000)...
netstat -ano | findstr :8000 > nul
if %errorlevel% neq 0 (
    echo [%TIME%] Server is DOWN. Launching Standalone Web and AI Core...
    start /b python -m uvicorn backend.main:app --host 0.0.0.0 --port 8000 --no-access-log
    ping localhost -n 8 > nul
) else (
    echo [%TIME%] AI Standalone Server is ACTIVE.
)

:RUN_SCRAPER
echo [%TIME%] Executing Intelligent Scraper Background Task...
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