<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi Akun</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6fa; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 24px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.05em; }
        .header p { color: #93c5fd; margin: 5px 0 0 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; }
        .body { padding: 32px 28px; }
        .greeting { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .otp-box { background: #eff6ff; border: 2px dashed #3b82f6; border-radius: 10px; padding: 18px; text-align: center; margin: 24px 0; }
        .otp-label { font-size: 11px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
        .otp-code { font-size: 32px; font-weight: 900; letter-spacing: 0.3em; color: #1d4ed8; font-family: 'Courier New', Courier, monospace; margin: 0; }
        .warning { font-size: 12px; color: #64748b; background: #f8fafc; border-left: 3px solid #cbd5e1; padding: 12px; margin-top: 20px; line-height: 1.5; }
        .footer { background: #f8fafc; padding: 18px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT. ASIA PLASTIK</h1>
            <p>Corporate Ticketing & Communication System</p>
        </div>
        <div class="body">
            <div class="greeting">Halo, {{ $userName }}!</div>
            <div class="text">
                Terima kasih telah mendaftar di <strong>Portal Ticketing & Komunikasi Internal PT. Asia Plastik</strong>.
                Untuk menyelesaikan pendaftaran dan mengaktifkan akun Anda, silakan gunakan kode One-Time Password (OTP) berikut:
            </div>
            
            <div class="otp-box">
                <div class="otp-label">Kode Verifikasi Keamanan (OTP)</div>
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <div class="text" style="font-size: 13px;">
                Kode ini berlaku selama <strong>15 menit</strong>. Jangan berikan kode ini kepada siapapun demi keamanan data perusahaan Anda.
            </div>

            <div class="warning">
                Jika Anda tidak merasa melakukan pendaftaran ini, abaikan email ini atau hubungi tim IT Support di internal kantor.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PT. ASIA PLASTIK &bull; Rungkut Industri, Surabaya<br>
            Sistem Otomatisasi & Komunikasi Terpadu
        </div>
    </div>
</body>
</html>
