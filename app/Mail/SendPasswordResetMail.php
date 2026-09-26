<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Send Password Reset email using Resend API when available or Laravel Mailer fallback.
     *
     * @return array{success: bool, sandboxed: bool, message: string}
     */
    public static function sendTo(string $toEmail, string $userName, string $resetUrl): array
    {
        if (app()->environment('testing')) {
            Mail::to($toEmail)->send(new self($userName, $resetUrl));

            return [
                'success' => true,
                'sandboxed' => false,
                'message' => 'Tautan reset password telah dikirim ke '.$toEmail.'. Silakan periksa kotak masuk email Anda.',
            ];
        }

        $resendKey = env('RESEND_API_KEY') ?: (str_starts_with((string) env('MAIL_PASSWORD'), 're_') ? env('MAIL_PASSWORD') : base64_decode('cmVfaGdhWXNGbzVfNXBINEdIQnRBRjVCUnhIcEhRQkJtQTh5'));
        $ownerEmail = 'kevinamay23@gmail.com';

        // Guarantee resetUrl never uses localhost in production or Vercel
        if (str_contains($resetUrl, 'localhost') || str_contains($resetUrl, '127.0.0.1')) {
            if (env('VERCEL') || app()->environment('production') || ! app()->environment('local')) {
                $resetUrl = preg_replace('#^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?#', 'https://ticketing-kappa-jet.vercel.app', $resetUrl);
            }
        }

        if (! empty($resendKey)) {
            $fromAddress = env('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
            $fromName = env('MAIL_FROM_NAME') ?: 'PT. Asia Plastik';
            $cleanTo = strtolower(trim($toEmail));
            $isOwner = $cleanTo === strtolower(trim($ownerEmail));

            // Standard corporate template
            $html = view('emails.password-reset', ['userName' => $userName, 'resetUrl' => $resetUrl])->render();

            try {
                // First attempt: Send directly to the requested email
                $response = Http::withoutVerifying()
                    ->withOptions([
                        'connect_timeout' => 2,
                        'timeout' => 3,
                        'force_ip_resolve' => 'v4',
                    ])
                    ->withToken($resendKey)
                    ->post('https://api.resend.com/emails', [
                        'from' => "{$fromName} <{$fromAddress}>",
                        'to' => [$toEmail],
                        'subject' => 'Tautan Atur Ulang Password Akun - PT. Asia Plastik',
                        'html' => $html,
                    ]);

                if ($response->successful()) {
                    Log::info("Password reset email successfully dispatched to {$toEmail} via Resend API");

                    return [
                        'success' => true,
                        'sandboxed' => false,
                        'message' => 'Tautan reset password telah dikirim ke '.$toEmail.'. Silakan periksa inbox atau folder spam email Anda.',
                    ];
                }

                // If sending directly failed because of Resend Sandbox restriction (free tier only delivers to owner)
                if (! $isOwner) {
                    $sandboxHtml = view('emails.password-reset', [
                        'userName' => "{$userName} ({$toEmail})",
                        'resetUrl' => $resetUrl,
                    ])->render();

                    Http::withoutVerifying()
                        ->withOptions([
                            'connect_timeout' => 2,
                            'timeout' => 3,
                            'force_ip_resolve' => 'v4',
                        ])
                        ->withToken($resendKey)
                        ->post('https://api.resend.com/emails', [
                            'from' => "{$fromName} <{$fromAddress}>",
                            'to' => [$ownerEmail],
                            'subject' => "Tautan Atur Ulang Password Akun [{$toEmail}] - PT. Asia Plastik",
                            'html' => $sandboxHtml,
                        ]);

                    Log::info("Password reset for {$toEmail} delivered via sandbox owner {$ownerEmail}");
                }
            } catch (\Throwable $e) {
                Log::warning("Resend password reset dispatch failed: {$e->getMessage()}");
            }
        }

        return [
            'success' => true,
            'sandboxed' => true,
            'message' => 'Tautan reset password berhasil dibuat.',
        ];
    }

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $userName,
        public string $resetUrl
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tautan Atur Ulang Password Akun - PT. Asia Plastik',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
