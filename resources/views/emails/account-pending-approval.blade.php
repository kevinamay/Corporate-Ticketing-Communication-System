<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Berhasil - Menunggu Konfirmasi Admin IT</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px 12px; color: #1e293b; }
        .container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); padding: 28px 24px; text-align: center; border-bottom: 3px solid #3b82f6; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; }
        .header p { color: #93c5fd; margin: 6px 0 0 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 600; }
        .body { padding: 32px 28px; }
        .badge-notice { display: inline-block; background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 6px 14px; border-radius: 9999px; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0; line-height: 1.3; }
        .text { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .card-alert { background: #fffbeb; border: 1px solid #fef08a; border-left: 4px solid #f59e0b; border-radius: 10px; padding: 16px; margin: 24px 0; }
        .card-alert-title { font-size: 12px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
        .card-alert-text { font-size: 13px; color: #92400e; line-height: 1.5; margin: 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 22px 0; background: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .info-table tr { border-bottom: 1px solid #e2e8f0; }
        .info-table tr:last-child { border-bottom: none; }
        .info-table td { padding: 12px 16px; font-size: 13px; }
        .info-label { width: 40%; font-weight: 600; color: #64748b; }
        .info-value { width: 60%; font-weight: 700; color: #0f172a; }
        .status-badge { display: inline-block; background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
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
            <span class="badge-notice">&#9200; Menunggu Konfirmasi Admin</span>
            
            <h2 class="title">Pendaftaran Akun Berhasil Dikirim!</h2>
            
            <p class="text">
                Halo <strong>{{ $userName }}</strong>,<br><br>
                Terima kasih telah mendaftar di <strong>Portal Ticketing & Layanan Operasional PT. Asia Plastik</strong>. Data akun karyawan Anda telah berhasil kami simpan ke dalam sistem.
            </p>

            <!-- Alert Card -->
            <div class="card-alert">
                <div class="card-alert-title">Status: Menunggu ACC / Konfirmasi Admin IT</div>
                <p class="card-alert-text">
                    Sesuai dengan kebijakan keamanan internal perusahaan, akun baru yang didaftarkan <strong>belum aktif</strong> dan <strong>belum dapat digunakan untuk login</strong> sampai pihak Administrator IT menyetujui (meng-ACC) akun Anda.
                </p>
            </div>

            <!-- Detail Pendaftaran -->
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama Lengkap</td>
                    <td class="info-value">{{ $userName }}</td>
                </tr>
                <tr>
                    <td class="info-label">Email Terdaftar</td>
                    <td class="info-value">{{ $userEmail }}</td>
                </tr>
                <tr>
                    <td class="info-label">Nomor WhatsApp / HP</td>
                    <td class="info-value">{{ $userPhone ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Status Akun</td>
                    <td class="info-value">
                        <span class="status-badge">&#9679; Belum Aktif (Menunggu ACC)</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Waktu Pendaftaran</td>
                    <td class="info-value">{{ $registeredAt ?? \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y, H:i \W\I\B') }}</td>
                </tr>
            </table>

            <p class="text" style="font-size: 13px; color: #64748b;">
                <strong>Langkah Selanjutnya:</strong><br>
                Administrator IT akan segera memverifikasi data Anda. Begitu akun Anda disetujui (di-ACC), Anda akan secara otomatis menerima <strong>email pemberitahuan aktivasi</strong> beserta tautan untuk masuk ke sistem.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} PT. ASIA PLASTIK &bull; Rungkut Industri, Surabaya<br>
            Pemberitahuan Otomatis Sistem Registrasi Karyawan
        </div>
    </div>
</body>
</html>
