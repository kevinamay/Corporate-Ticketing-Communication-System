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
     * Send a WhatsApp notification to IT Admin via official Meta WhatsApp Cloud API (Graph API).
     * Designed for serverless environments (e.g. Vercel) with fail-safe error handling.
     */
    public function sendNewTicketNotification(Ticket $ticket): bool
    {
        try {
            $phoneNumberId = config('services.meta_whatsapp.phone_number_id') ?? env('META_WA_PHONE_NUMBER_ID');
            $accessToken = config('services.meta_whatsapp.access_token') ?? env('META_WA_ACCESS_TOKEN');
            $adminNumber = config('services.meta_whatsapp.admin_number') ?? env('WA_ADMIN_NUMBER');
            $apiVersion = config('services.meta_whatsapp.api_version') ?? env('META_WA_API_VERSION', 'v20.0');

            if (empty($phoneNumberId) || empty($accessToken) || empty($adminNumber)) {
                Log::info("WhatsApp notification skipped for ticket #{$ticket->id}: META_WA_PHONE_NUMBER_ID, META_WA_ACCESS_TOKEN, or WA_ADMIN_NUMBER is missing.");

                return false;
            }

            // Eager-load relations for message formatting
            $ticket->loadMissing(['sender.department', 'targetDepartment', 'user']);

            $message = $this->buildNewTicketMessage($ticket);
            $formattedRecipient = $this->formatPhoneNumber($adminNumber);

            $endpoint = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages";

            // Standard Meta WhatsApp Cloud API Text Message Payload
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedRecipient,
                'type' => 'text',
                'text' => [
                    'body' => $message,
                ],
            ];

            // Execute HTTP POST request with strict 5-second timeout for serverless
            $response = Http::timeout(5)
                ->withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("Meta WhatsApp notification sent successfully for ticket #{$ticket->id} to {$formattedRecipient}");

                return true;
            }

            Log::warning("Meta WhatsApp Cloud API error for ticket #{$ticket->id} [Status {$response->status()}]: ".$response->body());

            return false;
        } catch (\Throwable $e) {
            // Fail silently so user ticket submission is NEVER interrupted or thrown on Vercel
            Log::error("Failed to send Meta WhatsApp notification for ticket #{$ticket->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Build the message template:
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

        // 2. Department Name
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
     * Normalize phone number to official E.164 international format required by Meta (without leading + or 0).
     * Example: '081234567890' -> '6281234567890', '+6281234567890' -> '6281234567890'
     */
    public function formatPhoneNumber(string $number): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        if (str_starts_with($cleaned, '0')) {
            return '62'.substr($cleaned, 1);
        }

        return '62'.$cleaned;
    }
}
