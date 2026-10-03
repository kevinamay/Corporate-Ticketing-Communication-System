<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Berhasil Dikonfirmasi & Aktif - PT. Asia Plastik</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px 12px; color: #1e293b; }
        .container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #065f46 100%); padding: 28px 24px; text-align: center; border-bottom: 3px solid #10b981; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; }
        .header p { color: #a7f3d0; margin: 6px 0 0 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 600; }
        .body { padding: 32px 28px; }
        .badge-notice { display: inline-block; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 6px 14px; border-radius: 9999px; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0; line-height: 1.3; }
        .text { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .card-success { background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid #10b981; border-radius: 10px; padding: 16px; margin: 24px 0; }
        .card-success-title { font-size: 12px; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
        .card-success-text { font-size: 13px; color: #065f46; line-height: 1.5; margin: 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 22px 0; background: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .info-table tr { border-bottom: 1px solid #e2e8f0; }
        .info-table tr:last-child { border-bottom: none; }
        .info-table td { padding: 12px 16px; font-size: 13px; }
        .info-label { width: 40%; font-weight: 600; color: #64748b; }
        .info-value { width: 60%; font-weight: 700; color: #0f172a; }
        .status-badge { display: inline-block; background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .btn-wrapper { text-align: center; margin: 30px 0 15px 0; }
        .btn { display: inline-block; background-color: #0284c7; color: #ffffff !important; padding: 14px 34px; border-radius: 10px; font-size: 14px; font-weight: 800; text-decoration: none; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35); }
        .footer { background: #f8fafc; padding: 22px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PT. ASIA PLASTIK</h1>
            <p>Corporate Ticketing & Communication System</p>
        </div>

        <!-- Content -->
        <div class="body">
            <span class="badge-notice">&#10004; Akun Telah Di-ACC & Aktif</span>
            
            <h2 class="title">Selamat! Akun Anda Telah Dikonfirmasi</h2>
            
            <p class="text">
                Halo <strong>{{ $userName }}</strong>,<br><br>
                Kabar gembira! Akun karyawan Anda di <strong>Portal Ticketing & Layanan Operasional PT. Asia Plastik</strong> telah berhasil <strong>disetujui (di-ACC)</strong> oleh Administrator IT.
            </p>

            <!-- Success Card -->
            <div class="card-success">
                <div class="card-success-title">Status: Terkonfirmasi & Aktif Sepenuhnya</div>
                <p class="card-success-text">
                    Akun Anda sekarang telah aktif. Anda dapat langsung masuk (login) ke sistem untuk mengajukan tiket dukungan kendala teknis, memantau pengerjaan, dan berkoordinasi dengan tim IT Support.
                </p>
            </div>

            <!-- Detail Akun -->
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama Lengkap</td>
                    <td class="info-value">{{ $userName }}</td>
                </tr>
                <tr>
                    <td class="info-label">Email Login</td>
                    <td class="info-value">{{ $userEmail }}</td>
                </tr>
                <tr>
                    <td class="info-label">Status Akses</td>
                    <td class="info-value">
                        <span class="status-badge">&#9679; Aktif / Terverifikasi</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Waktu Konfirmasi</td>
                    <td class="info-value">{{ date('d M Y, H:i') }} WIB</td>
                </tr>
            </table>

            <!-- Button CTA -->
            <div class="btn-wrapper">
                <a href="{{ $loginUrl }}" target="_blank" class="btn">Masuk ke Portal Sekarang &rarr;</a>
            </div>

            <p class="text" style="font-size: 12px; color: #64748b; text-align: center; margin-top: 15px;">
                Gunakan email Anda (<strong>{{ $userEmail }}</strong>) dan password yang Anda tentukan saat pendaftaran.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} PT. ASIA PLASTIK &bull; Rungkut Industri, Surabaya<br>
            Sistem Otomatisasi & Komunikasi Terpadu
        </div>
    </div>
</body>
</html>
