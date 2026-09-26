<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password Akun</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6fa; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 26px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.05em; }
        .header p { color: #93c5fd; margin: 6px 0 0 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; }
        .body { padding: 32px 28px; }
        .greeting { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .btn-wrapper { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff !important; padding: 14px 32px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 4px 10px rgba(37,99,235,0.3); }
        .link-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; word-break: break-all; font-size: 11px; color: #64748b; font-family: monospace; margin-top: 15px; }
        .warning { font-size: 12px; color: #64748b; background: #f8fafc; border-left: 3px solid #cbd5e1; padding: 12px; margin-top: 24px; line-height: 1.5; }
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
                Kami menerima permohonan untuk mengatur ulang kata sandi (password) akun Anda di <strong>Portal Ticketing & Komunikasi Internal PT. Asia Plastik</strong>.
            </div>
            <div class="text">
                Silakan klik tombol di bawah ini untuk membuat password baru akun Anda:
            </div>
            
            <div class="btn-wrapper">
                <a href="{{ $resetUrl }}" target="_blank" class="btn">Atur Ulang Password Saya</a>
            </div>

            <div class="text" style="font-size: 12px; color: #64748b;">
                Atau salin tautan berikut ke browser Anda jika tombol di atas tidak dapat diklik:
                <div class="link-box">{{ $resetUrl }}</div>
            </div>

            <div class="text" style="font-size: 13px; margin-top: 20px;">
                Tautan reset password ini berlaku selama <strong>60 menit</strong>.
            </div>

            <div class="warning">
                Jika Anda tidak merasa melakukan permohonan ini, akun Anda tetap aman dan Anda dapat mengabaikan email ini.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PT. ASIA PLASTIK &bull; Rungkut Industri, Surabaya<br>
            Sistem Otomatisasi & Komunikasi Terpadu
        </div>
    </div>
</body>
</html>
