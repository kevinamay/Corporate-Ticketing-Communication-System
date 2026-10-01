# WhatsApp Notification Gateway (WAHA) untuk Corporate Ticketing System

Folder ini berisi seluruh komponen, konfigurasi Docker, dan helper service untuk mengintegrasikan notifikasi WhatsApp otomatis ke dalam sistem **Corporate Ticketing Communication System** tanpa mengganggu arsitektur program yang sudah berjalan.

---

## 📁 Struktur Modul

* **`docker-compose.yml`** : Konfigurasi Docker Compose untuk menjalankan WAHA container di port 3000 dengan persistent session storage.
* **`start-gateway.bat`** : Skrip 1-klik untuk menyalakan container WAHA di lingkungan Windows.
* **`scan_wa.html`** : Halaman pemindai QR Code interaktif dengan fitur auto-refresh real-time agar QR tidak kedaluwarsa saat di-scan.
* **`WhatsAppService.php`** : Service class Laravel siap pakai (*plug-and-play*) untuk mengirim notifikasi tiket baru dan update status tiket ke WhatsApp karyawan atau grup IT.

---

## 🚀 Cara Menjalankan WhatsApp Gateway

### 1. Jalankan Container WAHA
Pastikan Docker Desktop sudah menyala di laptop / server Anda, lalu jalankan perintah berikut di PowerShell atau Command Prompt:
```bash
docker run -d --name waha -p 3000:3000 -v waha_sessions:/app/.sessions --restart unless-stopped devlikeapro/waha
```
*Atau cukup klik dua kali file `start-gateway.bat`.*

### 2. Tautkan WhatsApp (Scan QR Code)
1. Buka file **`scan_wa.html`** langsung di browser (klik dua kali filenya).
2. Di HP Anda, buka **WhatsApp** > **Perangkat Tertaut (Linked Devices)** > **Tautkan Perangkat**.
3. Arahkan kamera HP ke QR Code yang tampil di browser.
4. Begitu terhubung, tampilan halaman otomatis berubah menjadi **"WhatsApp Terhubung!"**.

---

## 🛠️ Konfigurasi Laravel (`.env`)

Tambahkan variabel berikut ke dalam file `.env` proyek Laravel Anda:

```env
WAHA_BASE_URL=http://localhost:3000
WAHA_API_KEY=e8928adf08ec4cfd8b30dea033ee38bc
WAHA_SESSION=default
WHATSAPP_IT_ADMIN_NUMBER=081234567890
```

---

## 💻 Cara Integrasi ke Controller Laravel

Pindahkan atau panggil `WhatsAppService.php` pada folder `app/Services/` di aplikasi Laravel.

### Contoh 1: Notifikasi Saat Karyawan Membuat Tiket Baru
Tambahkan pemanggilan service ini di fungsi `store` pada `TicketController.php`:

```php
use App\Services\WhatsAppService;

public function store(Request $request, WhatsAppService $wa)
{
    // 1. Simpan tiket seperti biasa (logic asli tetap aman)
    $ticket = Ticket::create([
        'title'       => $request->title,
        'category'    => $request->category,
        'priority'    => $request->priority,
        'description' => $request->description,
        'user_id'     => auth()->id(),
    ]);

    // 2. Kirim notifikasi WA ke Tim IT / Admin (tanpa menghentikan proses jika WA offline)
    try {
        $adminPhone = env('WHATSAPP_IT_ADMIN_NUMBER', '081234567890');
        $wa->sendTicketCreatedNotification([
            'ticket_id'     => $ticket->id,
            'title'         => $ticket->title,
            'category'      => $ticket->category,
            'priority'      => $ticket->priority,
            'reporter_name' => auth()->user()->name,
            'description'   => $ticket->description,
        ], $adminPhone);
    } catch (\Exception $e) {
        // Log error jika ada kendala jaringan tanpa menggagalkan pembuatan tiket
        \Log::error('WA Notification Error: ' . $e->getMessage());
    }

    return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibuat dan notifikasi WA terkirim ke Tim IT!');
}
```

### Contoh 2: Notifikasi Saat Status Tiket Diperbarui oleh Teknisi IT
```php
public function updateStatus(Request $request, $id, WhatsAppService $wa)
{
    $ticket = Ticket::findOrFail($id);
    $ticket->status = $request->status;
    $ticket->save();

    // Kirim update ke WA pelapor tiket
    if ($ticket->user && $ticket->user->phone_number) {
        $wa->sendTicketStatusUpdatedNotification(
            ['id' => $ticket->id, 'title' => $ticket->title],
            $ticket->user->phone_number,
            $ticket->status,
            $request->note ?? null
        );
    }

    return back()->with('success', 'Status tiket diperbarui!');
}
```

---

## 🔑 Kredensial Default WAHA Dashboard
* **Dashboard URL** : `http://localhost:3000/dashboard`
* **Username** : `admin`
* **Password** : `ea7ef3f15cce4d88ac7629c4d9f162e9`
* **API Key** : `e8928adf08ec4cfd8b30dea033ee38bc`
