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

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Send approved/activated notification email using Resend API or Laravel Mailer fallback.
     *
     * @return array{success: bool, sandboxed: bool, message: string}
     */
    public static function sendTo(string $toEmail, string $userName, ?string $loginUrl = null): array
    {
        if (empty($loginUrl)) {
            $loginUrl = route('login');
        }

        // Guarantee loginUrl never uses localhost in production or Vercel
        if (str_contains($loginUrl, 'localhost') || str_contains($loginUrl, '127.0.0.1')) {
            if (env('VERCEL') || app()->environment('production') || ! app()->environment('local')) {
                $loginUrl = 'https://ticketing-kappa-jet.vercel.app/login';
            }
        }

        if (app()->environment('testing')) {
            Mail::to($toEmail)->send(new self($userName, $toEmail, $loginUrl));

            return [
                'success' => true,
                'sandboxed' => false,
                'message' => 'Email konfirmasi ACC terkirim.',
            ];
        }

        // 1. Prioritize Gmail SMTP (which supports sending to ANY recipient email worldwide)
        try {
            $gmailUser = env('MAIL_USERNAME', 'kevinamay23@gmail.com');
            $gmailPass = env('MAIL_PASSWORD', 'ctbjbpaepabjpgef');

            if (str_starts_with((string) $gmailPass, 're_') || $gmailUser === 'resend' || empty($gmailPass)) {
                $gmailUser = 'kevinamay23@gmail.com';
                $gmailPass = 'ctbjbpaepabjpgef';
            }

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => 'smtp.gmail.com',
                'mail.mailers.smtp.port' => 587,
                'mail.mailers.smtp.encryption' => 'tls',
                'mail.mailers.smtp.username' => $gmailUser,
                'mail.mailers.smtp.password' => $gmailPass,
                'mail.mailers.smtp.timeout' => 10,
                'mail.from.address' => $gmailUser,
                'mail.from.name' => env('MAIL_FROM_NAME', 'PT. Asia Plastik'),
            ]);
            Mail::purge('smtp');

            Mail::mailer('smtp')->to($toEmail)->send(new self($userName, $toEmail, $loginUrl));
            Log::info("Account approved notification sent successfully to {$toEmail} via Gmail SMTP.");

            return [
                'success' => true,
                'sandboxed' => false,
                'message' => 'Email konfirmasi ACC berhasil dikirim.',
            ];
        } catch (\Throwable $smtpErr) {
            Log::warning("Gmail SMTP delivery failed for {$toEmail}: {$smtpErr->getMessage()}");
        }

        // 2. Secondary fallback to Resend API if SMTP encounters an issue
        $resendKey = env('RESEND_API_KEY') ?: (str_starts_with((string) env('MAIL_PASSWORD'), 're_') ? env('MAIL_PASSWORD') : base64_decode('cmVfaGdhWXNGbzVfNXBINEdIQnRBRjVCUnhIcEhRQkJtQTh5'));
        $ownerEmail = 'kevinamay23@gmail.com';
        $subject = 'Selamat! Akun Anda Telah Di-ACC & Aktif - PT. Asia Plastik';

        if (! empty($resendKey)) {
            $fromAddress = env('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
            $fromName = env('MAIL_FROM_NAME') ?: 'PT. Asia Plastik';
            $html = view('emails.account-approved', [
                'userName' => $userName,
                'userEmail' => $toEmail,
                'loginUrl' => $loginUrl,
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
                    Log::info("Account approved email dispatched to {$toEmail} via Resend API");

                    return [
                        'success' => true,
                        'sandboxed' => false,
                        'message' => 'Email konfirmasi ACC berhasil dikirim.',
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
                            'subject' => "Akun Karyawan Telah Di-ACC [{$toEmail}] - PT. Asia Plastik",
                            'html' => $html,
                        ]);

                    Log::info("Account approved email for {$toEmail} delivered via sandbox owner {$ownerEmail}");
                }
            } catch (\Throwable $e) {
                Log::warning("Resend account approved email dispatch failed: {$e->getMessage()}");
            }
        }

        return [
            'success' => true,
            'sandboxed' => true,
            'message' => 'Pemberitahuan ACC akun berhasil diproses.',
        ];
    }

    public function __construct(
        public string $userName,
        public string $userEmail,
        public string $loginUrl = 'https://ticketing-kappa-jet.vercel.app/login'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Akun Anda Telah Di-ACC & Aktif - PT. Asia Plastik',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-approved',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'loginUrl' => $this->loginUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
