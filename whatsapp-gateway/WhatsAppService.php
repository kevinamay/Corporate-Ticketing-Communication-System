<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class WhatsAppService
 *
 * Service mandiri untuk integrasi WhatsApp Notification pada Corporate Ticketing System
 * menggunakan WAHA (WhatsApp HTTP API).
 */
class WhatsAppService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $session;

    public function __construct()
    {
        $this->baseUrl = config('services.waha.base_url', env('WAHA_BASE_URL', 'http://localhost:3000'));
        $this->apiKey = config('services.waha.api_key', env('WAHA_API_KEY', 'e8928adf08ec4cfd8b30dea033ee38bc'));
        $this->session = config('services.waha.session', env('WAHA_SESSION', 'default'));
    }

    /**
     * Kirim pesan teks WhatsApp ke nomor tujuan atau grup.
     *
     * @param  string  $to  Nomor HP (contoh: '08123456789' atau '628123456789') atau ID Grup (contoh: '12036304@g.us')
     * @param  string  $message  Isi pesan yang akan dikirim
     * @return array Status pengiriman
     */
    public function sendMessage(string $to, string $message): array
    {
        $chatId = $this->formatPhoneNumber($to);

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$this->baseUrl}/api/sendText", [
                'chatId' => $chatId,
                'text' => $message,
                'session' => $this->session,
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Notification Error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi tiket baru ke Tim IT / Admin Helpdesk.
     *
     * @param  array  $ticket  Data tiket (id, title, category, priority, reporter_name, description)
     * @param  string  $targetPhone  Nomor WA IT Support atau ID Grup WA IT Support
     */
    public function sendTicketCreatedNotification(array $ticket, string $targetPhone): array
    {
        $ticketId = $ticket['ticket_id'] ?? $ticket['id'] ?? '-';
        $title = $ticket['title'] ?? $ticket['subject'] ?? 'Tiket Baru';
        $category = $ticket['category'] ?? 'Umum';
        $priority = $ticket['priority'] ?? 'Normal';
        $reporter = $ticket['reporter_name'] ?? $ticket['user_name'] ?? 'Karyawan';
        $description = $ticket['description'] ?? '-';

        $text = "🔔 *TIKET HELPDESK BARU TELAH MASUK*\n"
              ."━━━━━━━━━━━━━━━━━━━━\n"
              ."🆔 *Nomor Tiket :* #{$ticketId}\n"
              ."👤 *Pelapor     :* {$reporter}\n"
              ."📂 *Kategori    :* {$category}\n"
              ."⚡ *Prioritas   :* {$priority}\n"
              ."📝 *Judul       :* {$title}\n\n"
              ."💬 *Deskripsi   :*\n{$description}\n"
              ."━━━━━━━━━━━━━━━━━━━━\n"
              .'⚠️ _Mohon Tim IT untuk segera menindaklanjuti tiket ini di sistem._';

        return $this->sendMessage($targetPhone, $text);
    }

    /**
     * Kirim notifikasi update status tiket ke Karyawan yang melapor.
     *
     * @param  array  $ticket  Data tiket
     * @param  string  $reporterPhone  Nomor WA pelapor
     * @param  string  $newStatus  Status baru (contoh: 'Diproses', 'Selesai', 'Ditolak')
     * @param  string|null  $note  Catatan dari teknisi IT
     */
    public function sendTicketStatusUpdatedNotification(array $ticket, string $reporterPhone, string $newStatus, ?string $note = null): array
    {
        $ticketId = $ticket['ticket_id'] ?? $ticket['id'] ?? '-';
        $title = $ticket['title'] ?? $ticket['subject'] ?? 'Tiket';

        $text = "📢 *UPDATE STATUS TIKET HELPDESK*\n"
              ."━━━━━━━━━━━━━━━━━━━━\n"
              ."🆔 *Nomor Tiket :* #{$ticketId}\n"
              ."📝 *Judul       :* {$title}\n"
              ."🔄 *Status Baru :* *{$newStatus}*\n";

        if (! empty($note)) {
            $text .= "💬 *Catatan IT   :* {$note}\n";
        }

        $text .= "━━━━━━━━━━━━━━━━━━━━\n"
              .'ℹ️ _Terima kasih atas laporan Anda. Pantau perkembangan tiket melalui aplikasi helpdesk._';

        return $this->sendMessage($reporterPhone, $text);
    }

    /**
     * Helper untuk memformat nomor telepon Indonesia menjadi format JID WhatsApp.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        if (str_contains($phone, '@g.us')) {
            return $phone;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        }

        if (! str_contains($cleaned, '@c.us')) {
            $cleaned .= '@c.us';
        }

        return $cleaned;
    }
}
