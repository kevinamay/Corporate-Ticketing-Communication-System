# WhatsApp Notification & Chat Gateway - PT. Asia Plastik

Modul ini adalah sistem mandiri (*standalone module*) untuk mengirimkan pesan WhatsApp dan notifikasi tiket otomatis langsung ke HP karyawan, staf, atau IT Support tanpa mengganggu atau merusak aplikasi web Ticketing yang sudah dideploy di Vercel (`https://ticketing-kappa-jet.vercel.app`).

---

## 📁 Isi File Modul `whatsapp-gateway/`

| File | Fungsi |
| :--- | :--- |
| **`index.html`** / **`WhatsApp_Chat_Gateway.html`** | **Dashboard & Tester Interaktif**: Layar untuk menghubungkan WhatsApp (QR / Pairing Code) dan langsung mencoba kirim notifikasi tiket ke HP secara real-time. |
| **`start-gateway.bat`** | Skrip 1-klik untuk menyalakan container WhatsApp Gateway (Docker WAHA). |
| **`docker-compose.yml`** | Konfigurasi Docker Compose resmi dengan mesin `NOWEB` yang stabil dan hemat memori. |
| **`WhatsAppService.php`** | Class helper siap pakai untuk pengiriman pesan dari aplikasi PHP / Laravel. |

---

## 🚀 Panduan Step-by-Step Cara Menjalankan & Mencoba Program

### Langkah 1: Pastikan Docker Desktop Menyala
Pastikan Docker Desktop di Windows Anda sudah berjalan (*Engine running* warna hijau).

### Langkah 2: Nyalakan WhatsApp Gateway
Klik dua kali file **`start-gateway.bat`** di dalam folder `whatsapp-gateway/` (atau container `waha` sudah otomatis menyala di port `3000`).

### Langkah 3: Buka Layar WhatsApp Gateway
Klik dua kali file **`WhatsApp_Chat_Gateway.html`** di Desktop Anda (atau buka `whatsapp-gateway/index.html` di browser).

### Langkah 4: Hubungkan Nomor WhatsApp (Hanya 1 Kali)
Di panel kiri layar:
* **Metode Rekomendasi (Kode Tautan 8 Digit - Bebas Masalah Kamera):**
  1. Klik tab **"Metode 2: Kode Tautan"**.
  2. Ketik nomor WhatsApp Anda (awalan `62`, contoh: `6281234567890`) lalu klik **Dapatkan Kode Pairing**.
  3. Di HP Anda, buka WhatsApp > **Perangkat tertaut** > **Tautkan perangkat** > klik tautan biru **"Tautkan dengan nomor telepon saja"**.
  4. Masukkan kode 8 digit yang muncul di layar laptop. WhatsApp langsung terhubung!
* **Metode Scan QR:**
  Buka WhatsApp di HP > **Perangkat tertaut** > **Tautkan perangkat** > Arahkan kamera ke QR Code di layar.

### Langkah 5: Uji Coba Kirim Chat & Notifikasi Tiket Langsung
Di panel kanan layar:
1. Masukkan nomor WhatsApp tujuan (misal nomor HP Anda sendiri: `08xxxxxxxxxx`).
2. Pilih format pesan (contoh: *🔔 Notifikasi Tiket Baru* atau ketik pesan kustom).
3. Klik tombol hijau **"🚀 KIRIM KE WHATSAPP SEKARANG"**.
4. **Buka HP Anda**: Pesan notifikasi tiket resmi PT. Asia Plastik langsung masuk ke WhatsApp Anda dengan tautan langsung menuju web portal Vercel: `https://ticketing-kappa-jet.vercel.app`!

---

## 🌐 Integrasi API (HTTP POST)

Jika Anda ingin mengirim pesan WhatsApp dari sistem lain atau curl, cukup panggil API lokal:

* **Endpoint:** `POST http://localhost:3000/api/sendText`
* **Headers:**
  - `Content-Type: application/json`
  - `X-Api-Key: e8928adf08ec4cfd8b30dea033ee38bc`
* **JSON Payload:**
  ```json
  {
    "chatId": "6281234567890@c.us",
    "text": "🚨 *TIKET BARU MASUK!*\nSegera cek di https://ticketing-kappa-jet.vercel.app",
    "session": "default"
  }
  ```
