@echo off
title Launcher Corporate Ticketing & WhatsApp Gateway
color 0A
echo ========================================================
echo   MENYIAPKAN SISTEM CORPORATE TICKETING & WHATSAPP
echo ========================================================
echo.

echo [1/3] Memeriksa Database MySQL...
C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqladmin.exe -u root ping >nul 2>&1
if %errorlevel% neq 0 (
    echo Menyalakan MySQL Server di background...
    start "" /B C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe --defaults-file=C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini
    timeout /t 3 >nul
) else (
    echo MySQL Server sudah aktif!
)

echo.
echo [2/3] Memeriksa WhatsApp Gateway (Docker WAHA)...
docker ps | findstr /i "waha" >nul 2>&1
if %errorlevel% neq 0 (
    echo Menjalankan container Docker WAHA...
    docker start waha >nul 2>&1 || docker run -d --name waha -p 3000:3000 -e WHATSAPP_DEFAULT_ENGINE=NOWEB -e WAHA_API_KEY=e8928adf08ec4cfd8b30dea033ee38bc -e WAHA_DASHBOARD_USERNAME=admin -e WAHA_DASHBOARD_PASSWORD=ea7ef3f15cce4d88ac7629c4d9f162e9 -v waha_sessions:/app/.sessions --restart unless-stopped devlikeapro/waha
) else (
    echo Container WhatsApp WAHA sudah aktif!
)

echo.
echo [3/3] Membuka Aplikasi di Browser...
start http://localhost:8000
if exist "%~dp0whatsapp-gateway\scan_wa.html" (
    start "" "%~dp0whatsapp-gateway\scan_wa.html"
)

echo.
echo ========================================================
echo   SEMUA LAYANAN BERHASIL AKTIF!
echo   - Aplikasi Web : http://localhost:8000
echo   - WhatsApp QR  : whatsapp-gateway\scan_wa.html
echo ========================================================
echo.
cd /d "%~dp0"
php artisan serve --port=8000
pause
