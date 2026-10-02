@echo off
title WAHA WhatsApp Gateway Runner - PT. Asia Plastik
color 0A
echo =======================================================================
echo         WAHA WHATSAPP HTTP API GATEWAY - PT. ASIA PLASTIK
echo =======================================================================
echo.

:: 1. Periksa apakah Docker Engine sedang berjalan
docker info >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo [INFO] Docker Desktop belum aktif / sedang offline.
    echo [INFO] Membuka Docker Desktop, mohon tunggu sebentar...
    start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    
    echo.
    echo Menunggu Docker Desktop engine siap (ini membutuhkan waktu sekitar 15-30 detik)...
    set /a attempts=0
    :WAIT_DOCKER
    timeout /t 3 /nobreak >nul
    set /a attempts+=1
    docker info >nul 2>&1
    if %ERRORLEVEL% neq 0 (
        if %attempts% geq 25 (
            echo.
            echo [PERINGATAN] Docker Desktop belum siap atau memerlukan persetujuan Administrator.
            echo Silakan buka aplikasi 'Docker Desktop' dari menu Windows/Desktop Anda.
            echo Setelah Docker Desktop menyala hijau, jalankan kembali file ini.
            echo.
            pause
            exit /b 1
        )
        echo   Masih menunggu Docker Engine (%attempts%/25)...
        goto WAIT_DOCKER
    )
    echo [OK] Docker Desktop berhasil tersambung!
)

echo.
echo [INFO] Menjalankan container gateway WAHA di port 3000...
:: Jalankan container yang sudah ada jika ada, atau buat baru jika belum ada
docker start waha >nul 2>&1
if %ERRORLEVEL% neq 0 (
    docker run -d --name waha -p 3000:3000 -e WHATSAPP_DEFAULT_ENGINE=NOWEB -e WAHA_API_KEY=e8928adf08ec4cfd8b30dea033ee38bc -e WAHA_DASHBOARD_USERNAME=admin -e WAHA_DASHBOARD_PASSWORD=ea7ef3f15cce4d88ac7629c4d9f162e9 -v waha_sessions:/app/.sessions --restart unless-stopped devlikeapro/waha
)

echo.
echo =======================================================================
echo   WAHA WhatsApp Gateway Berhasil Berjalan!
echo   Port          : 3000
echo   Dashboard URL : http://localhost:3000/dashboard
echo   API Key       : e8928adf08ec4cfd8b30dea033ee38bc
echo   Nomor Bot     : 082244109503 (Pengirim)
echo   Nomor Admin   : 085784694910 (Penerima Paten)
echo =======================================================================
echo.
echo Membuka dashboard tester WhatsApp...
if exist "%~dp0index.html" (
    start "" "%~dp0index.html"
) else if exist "%USERPROFILE%\Desktop\WhatsApp_Chat_Gateway.html" (
    start "" "%USERPROFILE%\Desktop\WhatsApp_Chat_Gateway.html"
)
echo Selesai! Gateway aktif di latar belakang.
echo Jangan tutup jendela ini jika ingin memantau status.
echo.
pause
