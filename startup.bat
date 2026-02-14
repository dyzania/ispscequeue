@echo off
TITLE E-Queue System - Startup Master
echo ==========================================
echo    E-QUEUE SYSTEM - STARTUP MASTER
echo ==========================================
echo.

echo [1/2] Starting Sentiment Analysis Server...
echo.
echo NOTE: If you see an error about "running scripts is disabled", 
echo please run: Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser 
echo in an Administrator PowerShell window.
echo.
start "Sentiment Analysis Server" powershell -NoExit -ExecutionPolicy Bypass -Command "& { Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process -Force; .\venv\Scripts\Activate.ps1; cd sentiment_analysis; uvicorn app:app --host 127.0.0.1 --port 8000 }"

echo [2/2] Opening Application...

timeout /t 3 /nobreak > nul
start http://localhost/equeue-system/public/index.php

echo.
echo ==========================================
echo    SYSTEM IS RUNNING
echo    Please ensure XAMPP (Apache/MySQL) is active.
echo ==========================================
echo.
pause
