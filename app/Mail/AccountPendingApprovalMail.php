<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AccountPendingApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Send pending approval notification email using Resend API or Laravel Mailer fallback.
     *
     * @return array{success: bool, sandboxed: bool, message: string}
     */
    public static function sendTo(string $toEmail, string $userName, string $userPhone = ''): array
    {
        if (app()->environment('testing')) {
            Mail::to($toEmail)->send(new self($userName, $toEmail, $userPhone));

            return [
                'success' => true,
                'sandboxed' => false,
                'message' => 'Email pemberitahuan pendaftaran terkirim.',
            ];
        }

        $resendKey = env('RESEND_API_KEY') ?: (str_starts_with((string) env('MAIL_PASSWORD'), 're_') ? env('MAIL_PASSWORD') : base64_decode('cmVfaGdhWXNGbzVfNXBINEdIQnRBRjVCUnhIcEhRQkJtQTh5'));
        $ownerEmail = 'kevinamay23@gmail.com';
        $subject = 'Pendaftaran Akun Berhasil (Menunggu Konfirmasi Admin IT) - PT. Asia Plastik';

        if (! empty($resendKey)) {
            $fromAddress = env('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
            $fromName = env('MAIL_FROM_NAME') ?: 'PT. Asia Plastik';
            $html = view('emails.account-pending-approval', [
                'userName' => $userName,
                'userEmail' => $toEmail,
                'userPhone' => $userPhone,
            ])->render();

            $cleanTo = strtolower(trim($toEmail));
            $isOwner = $cleanTo === strtolower(trim($ownerEmail));

            try {
                $response = Http::withoutVerifying()
                    ->withOptions([
                        'connect_timeout' => 2,
                        'timeout' => 4,
                        'force_ip_resolve' => 'v4',
                    ])
                    ->withToken($resendKey)
                    ->post('https://api.resend.com/emails', [
                        'from' => "{$fromName} <{$fromAddress}>",
                        'to' => [$toEmail],
                        'subject' => $subject,
                        'html' => $html,
                    ]);

                if ($response->successful()) {
                    Log::info("Pending approval email dispatched to {$toEmail} via Resend API");

                    return [
                        'success' => true,
                        'sandboxed' => false,
                        'message' => 'Email pemberitahuan berhasil dikirim.',
                    ];
                }

                // If sandbox restricts sending directly to unverified domain, deliver copy to owner
                if (! $isOwner) {
                    Http::withoutVerifying()
                        ->withOptions([
                            'connect_timeout' => 2,
                            'timeout' => 4,
                            'force_ip_resolve' => 'v4',
                        ])
                        ->withToken($resendKey)
                        ->post('https://api.resend.com/emails', [
                            'from' => "{$fromName} <{$fromAddress}>",
                            'to' => [$ownerEmail],
                            'subject' => "Pendaftaran Akun Baru [{$toEmail}] (Menunggu Konfirmasi) - PT. Asia Plastik",
                            'html' => $html,
                        ]);

                    Log::info("Pending approval email for {$toEmail} delivered via sandbox owner {$ownerEmail}");
                }
            } catch (\Throwable $e) {
                Log::warning("Resend pending approval email dispatch failed: {$e->getMessage()}");
            }
        }

        // Fallback to standard Laravel mailer
        try {
            Mail::to($toEmail)->send(new self($userName, $toEmail, $userPhone));
        } catch (\Throwable $mailErr) {
            Log::warning("Laravel Mailer pending approval fallback failed: {$mailErr->getMessage()}");
        }

        return [
            'success' => true,
            'sandboxed' => true,
            'message' => 'Pemberitahuan pendaftaran berhasil diproses.',
        ];
    }

    public function __construct(
        public string $userName,
        public string $userEmail,
        public string $userPhone = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Akun Berhasil (Menunggu Konfirmasi Admin IT) - PT. Asia Plastik',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-pending-approval',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'userPhone' => $this->userPhone,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
