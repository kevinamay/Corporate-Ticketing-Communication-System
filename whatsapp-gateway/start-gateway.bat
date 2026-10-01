@echo off
title WAHA WhatsApp Gateway Runner
echo ========================================================
echo   MENJALANKAN WAHA WHATSAPP HTTP API GATEWAY
echo ========================================================
echo.
echo Menjalankan container Docker waha di port 3000...
docker run -d --name waha -p 3000:3000 -v waha_sessions:/app/.sessions --restart unless-stopped devlikeapro/waha

echo.
echo ========================================================
echo   WAHA Gateway Berhasil Dijalankan!
echo   Dashboard URL : http://localhost:3000/dashboard
echo   Username      : admin
echo   Password      : ea7ef3f15cce4d88ac7629c4d9f162e9
echo   API Key       : e8928adf08ec4cfd8b30dea033ee38bc
echo ========================================================
echo.
echo Buka scan_wa.html untuk melakukan scan WhatsApp.
pause
