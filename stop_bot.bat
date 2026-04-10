@echo off
title HENTIKAN AUTO-BOT (KILL SERVICE)
echo =========================================
echo  MENUTUP SEMUA BACKGROUND BOT YANG AKTIF
echo =========================================
echo.
echo Sedang menghentikan PHP background worker...
taskkill /F /IM php.exe
echo.
echo Bot berhasil dimatikan sepenuhnya.
pause
