@echo off
cd /d "%~dp0"
title AI Bot Scraper - IDX Screener (Background Worker)
color 0A

:RESTART_LOOP
cls
echo ====================================================
echo    AI STOCK SCREENER - BOT BACKGROUND SERVICE
echo    Status: Always-On Loop Activated
echo ====================================================
echo [%DATE% %TIME%] Memulai Bot Scraper...
echo.

"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" backend\scraper.php

echo.
echo ----------------------------------------------------
echo Bot terhenti. Mengaktifkan Restart Otomatis (10s)...
echo ----------------------------------------------------
timeout /t 10
goto RESTART_LOO