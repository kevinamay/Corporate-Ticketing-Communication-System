<?php

namespace App\Services;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppNotificationService
{
    /**
     * Send a WhatsApp notification to IT Admin when a new ticket is submitted.
     * Designed for serverless environments (e.g. Vercel) with fail-safe error handling.
     */
    public function sendNewTicketNotification(Ticket $ticket): bool
    {
        try {
            $token = config('services.whatsapp.token');
            $adminNumber = config('services.whatsapp.admin_number');
            $apiUrl = config('services.whatsapp.url', 'https://api.fonnte.com/send');

            if (empty($token) || empty($adminNumber)) {
                Log::info("WhatsApp notification skipped for ticket #{$ticket->id}: WA_API_TOKEN or WA_ADMIN_NUMBER is not set.");

                return false;
            }

            // Eager-load relations to build full message details
            $ticket->loadMissing(['sender.department', 'targetDepartment', 'user']);

            $message = $this->buildNewTicketMessage($ticket);

            // Execute HTTP request with strict timeout for serverless lifecycle
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post($apiUrl, [
                    'target' => $this->formatPhoneNumber($adminNumber),
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp notification sent for ticket #{$ticket->id} to {$adminNumber}");

                return true;
            }

            Log::warning("WhatsApp gateway response error for ticket #{$ticket->id} [Status {$response->status()}]: ".$response->body());

            return false;
        } catch (\Throwable $e) {
            // Fail silently so user ticket submission is NEVER interrupted or thrown on Vercel
            Log::error("Failed to send WhatsApp notification for ticket #{$ticket->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Build the exact message template required by corporate standards:
     *
     * 🚨 *TIKET BARU MASUK!* 🚨
     * 👤 *Pengirim:* {Employee Name}
     * 🏢 *Divisi:* {Department Name}
     * 📝 *Masalah:* {Ticket Subject/Description}
     * 🕒 *Waktu:* {Created At - formatted in WIB}
     *
     * Segera proses tiket ini dengan klik link berikut:
     * 👉 {Absolute URL to the admin ticket detail page}
     */
    public function buildNewTicketMessage(Ticket $ticket): string
    {
        // 1. Employee Name
        $employeeName = $ticket->sender?->name ?? $ticket->user?->name ?? 'Karyawan';

        // 2. Department Name (Sender's department or Target department)
        $departmentName = $ticket->sender?->department?->name
            ?? $ticket->targetDepartment?->name
            ?? 'Operasional';

        // 3. Ticket Subject / Description
        $cleanDesc = strip_tags($ticket->description ?? '');
        $shortDesc = Str::limit($cleanDesc, 100);
        $issueText = ! empty($shortDesc) && $shortDesc !== $ticket->title
            ? "{$ticket->title} - {$shortDesc}"
            : $ticket->title;

        // 4. Timestamp formatted in WIB (Asia/Jakarta, UTC+7)
        $createdAt = $ticket->created_at
            ? Carbon::parse($ticket->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i').' WIB'
            : Carbon::now('Asia/Jakarta')->translatedFormat('d M Y, H:i').' WIB';

        // 5. Absolute URL to admin ticket detail
        $directUrl = route('dashboard', ['ticket' => $ticket->id], true);

        return "🚨 *TIKET BARU MASUK!* 🚨\n"
            ."👤 *Pengirim:* {$employeeName}\n"
            ."🏢 *Divisi:* {$departmentName}\n"
            ."📝 *Masalah:* {$issueText}\n"
            ."🕒 *Waktu:* {$createdAt}\n\n"
            ."Segera proses tiket ini dengan klik link berikut:\n"
            ."👉 {$directUrl}";
    }

    /**
     * Normalize Indonesian phone number to international / gateway format.
     * Examples: '08123456789' -> '08123456789' (or '628123456789')
     */
    protected function formatPhoneNumber(string $number): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        if (str_starts_with($cleaned, '0')) {
            return $cleaned;
        }

        return '0'.$cleaned;
    }
}
