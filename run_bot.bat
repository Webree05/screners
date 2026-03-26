@echo off
title AI Bot Scraper - IDX Screener (Background Worker)
color 0A
echo.
echo ====================================================
echo    AI STOCK SCREENER - BOT BACKGROUND SERVICE
echo ====================================================
echo.
echo Bot ini bertugas menyedot data harga 900+ saham secara realtime
echo dan membuat file 'market_data.json' secara background.
echo JANGAN TUTUP JENDELA INI SELAMA SCREENER DIPAKAI!
echo.
echo Menghubungkan ke Yahoo Finance API (Spark Edition)...

:: Menggunakan PHP yang diinstall di Laragon langsung
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe backend\scraper.php

echo.
echo Bot terhenti atau terjadi kesalahan (Crash).
pause
