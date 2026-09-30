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
     * Phone Number ID registered in Meta Developer Dashboard.
     */
    protected ?string $phoneNumberId;

    /**
     * User/System Access Token for Meta Graph API.
     */
    protected ?string $accessToken;

    /**
     * Destination WhatsApp Phone Number (IT Admin).
     */
    protected ?string $adminNumber;

    /**
     * Graph API Version (defaults to v20.0).
     */
    protected string $apiVersion;

    public function __construct()
    {
        $this->phoneNumberId = config('services.meta_whatsapp.phone_number_id') ?? env('META_WA_PHONE_NUMBER_ID');
        $this->accessToken = config('services.meta_whatsapp.access_token') ?? env('META_WA_ACCESS_TOKEN');
        $this->adminNumber = config('services.meta_whatsapp.admin_number') ?? env('ADMIN_WA_NUMBER') ?? env('WA_ADMIN_NUMBER');
        $this->apiVersion = config('services.meta_whatsapp.api_version') ?? env('META_WA_API_VERSION', 'v20.0');
    }

    /**
     * Send the default approved "hello_world" template message to IT Admin.
     * Required when testing with a Meta Test Phone Number to bypass the 24-hr customer service window.
     *
     * @param  string|null  $recipient  Optional recipient number (defaults to ADMIN_WA_NUMBER)
     */
    public function sendHelloWorldTemplate(?string $recipient = null): bool
    {
        return $this->sendTemplateNotification(
            recipient: $recipient,
            templateName: 'hello_world',
            languageCode: 'en_US'
        );
    }

    /**
     * Send an approved Meta WhatsApp template notification.
     *
     * @param  string|null  $recipient  Recipient phone number (defaults to ADMIN_WA_NUMBER)
     * @param  string  $templateName  Name of approved template in Meta Developer dashboard
     * @param  string  $languageCode  Template language code (e.g. 'en_US', 'id')
     * @param  array<int, array<string, mixed>>  $components  Dynamic template components (body parameters, buttons, etc.)
     */
    public function sendTemplateNotification(
        ?string $recipient = null,
        string $templateName = 'hello_world',
        string $languageCode = 'en_US',
        array $components = []
    ): bool {
        try {
            $targetNumber = $recipient ?: $this->adminNumber;

            if (empty($this->phoneNumberId) || empty($this->accessToken) || empty($targetNumber)) {
                Log::warning('WhatsApp template notification skipped: Missing Meta API credentials or recipient phone number.');

                return false;
            }

            $formattedRecipient = $this->formatPhoneNumber($targetNumber);
            $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            // Official Meta WhatsApp Cloud API Template Payload
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedRecipient,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $languageCode,
                    ],
                ],
            ];

            if (! empty($components)) {
                $payload['template']['components'] = $components;
            }

            // HTTP POST with 5s timeout optimized for serverless functions (Vercel)
            $response = Http::timeout(5)
                ->withToken($this->accessToken)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("Meta WhatsApp template '{$templateName}' sent successfully to {$formattedRecipient}.", [
                    'message_id' => $response->json('messages.0.id'),
                ]);

                return true;
            }

            Log::warning("Meta WhatsApp Cloud API error [Status {$response->status()}]: ".$response->body());

            return false;
        } catch (\Throwable $e) {
            // Fail safely to prevent breaking serverless execution
            Log::error("Failed to send Meta WhatsApp template '{$templateName}': ".$e->getMessage());

            return false;
        }
    }

    /**
     * Send a WhatsApp notification to IT Admin via official Meta WhatsApp Cloud API.
     * Supports both free-form text messages and approved template messages.
     *
     * @param  bool  $sendAsTemplate  If true, sends the pre-approved hello_world template
     */
    public function sendNewTicketNotification(Ticket $ticket, bool $sendAsTemplate = false): bool
    {
        try {
            if ($sendAsTemplate) {
                return $this->sendHelloWorldTemplate($this->adminNumber);
            }

            if (empty($this->phoneNumberId) || empty($this->accessToken) || empty($this->adminNumber)) {
                Log::info("WhatsApp notification skipped for ticket #{$ticket->id}: Missing credentials or admin phone number.");

                return false;
            }

            // Eager-load relations for message formatting
            $ticket->loadMissing(['sender.department', 'targetDepartment', 'user']);

            $message = $this->buildNewTicketMessage($ticket);
            $formattedRecipient = $this->formatPhoneNumber($this->adminNumber);

            $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

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
                ->withToken($this->accessToken)
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
            // Fail silently so ticket creation is NEVER interrupted or thrown on Vercel
            Log::error("Failed to send Meta WhatsApp notification for ticket #{$ticket->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Build the message template for text notifications:
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
     * Example: '081234567890' -> '6281234567890', '+6285784694910' -> '6285784694910'
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
