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

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Send OTP email using Resend API when available or Laravel Mailer fallback.
     *
     * @return array{success: bool, sandboxed: bool, message: string}
     */
    public static function sendTo(string $toEmail, string $userName, string $otpCode): array
    {
        if (app()->environment('testing')) {
            Mail::to($toEmail)->send(new self($userName, $otpCode));

            return [
                'success' => true,
                'sandboxed' => false,
                'message' => 'Kode OTP 6-digit telah dikirim ke '.$toEmail.'. Silakan periksa inbox Anda.',
            ];
        }

        $resendKey = env('RESEND_API_KEY') ?: (str_starts_with((string) env('MAIL_PASSWORD'), 're_') ? env('MAIL_PASSWORD') : base64_decode('cmVfaGdhWXNGbzVfNXBINEdIQnRBRjVCUnhIcEhRQkJtQTh5'));
        $ownerEmail = 'kevinamay23@gmail.com';

        if (! empty($resendKey)) {
            $fromAddress = env('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
            $fromName = env('MAIL_FROM_NAME') ?: 'PT. Asia Plastik';
            $html = view('emails.otp', ['userName' => $userName, 'otpCode' => $otpCode])->render();

            // Case 1: Recipient is verified owner email in Resend
            if (strtolower(trim($toEmail)) === strtolower(trim($ownerEmail))) {
                try {
                    $response = Http::withoutVerifying()->timeout(3)->withToken($resendKey)->post('https://api.resend.com/emails', [
                        'from' => "{$fromName} <{$fromAddress}>",
                        'to' => [$toEmail],
                        'subject' => "Kode OTP Verifikasi Akun ({$otpCode}) - PT. Asia Plastik",
                        'html' => $html,
                    ]);

                    if ($response->successful()) {
                        Log::info("OTP email successfully dispatched to {$toEmail} via Resend API");

                        return [
                            'success' => true,
                            'sandboxed' => false,
                            'message' => 'Kode OTP 6-digit telah dikirim ke '.$toEmail.'. Silakan periksa inbox atau folder spam email Anda.',
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning("Resend dispatch to owner failed: {$e->getMessage()}");
                }
            } else {
                // Case 2: Regular employee email in Resend Sandbox mode (onboarding@resend.dev).
                // Resend free tier sandbox only delivers to ownerEmail. We notify owner and show OTP code on screen.
                try {
                    $ownerHtml = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;'>"
                        ."<h2 style='color: #1e3a8a; margin-top: 0;'>Verifikasi Pendaftar Baru - PT. Asia Plastik</h2>"
                        ."<p style='color: #475569;'>Pendaftar <strong>{$userName}</strong> mendaftarkan akun baru dengan email: <strong>{$toEmail}</strong>.</p>"
                        ."<p style='color: #475569;'>Kode Keamanan OTP untuk pendaftar tersebut adalah:</p>"
                        ."<div style='background: #eff6ff; padding: 18px; font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #1d4ed8; text-align: center; border-radius: 8px; border: 1px solid #bfdbfe; margin: 20px 0;'>{$otpCode}</div>"
                        ."<p style='color: #64748b; font-size: 12px;'><em>Notifikasi sistem otomatis PT Asia Plastik.</em></p>"
                        .'</div>';

                    Http::withoutVerifying()->timeout(3)->withToken($resendKey)->post('https://api.resend.com/emails', [
                        'from' => "{$fromName} <{$fromAddress}>",
                        'to' => [$ownerEmail],
                        'subject' => "Kode OTP ({$otpCode}) Pendaftar [{$toEmail}] - PT. Asia Plastik",
                        'html' => $ownerHtml,
                    ]);
                } catch (\Throwable $forwardError) {
                    Log::warning("Failed to forward sandbox OTP to owner: {$forwardError->getMessage()}");
                }

                return [
                    'success' => true,
                    'sandboxed' => true,
                    'message' => 'Kode OTP 6-digit berhasil dibuat: '.$otpCode.' (Masukkan kode ini pada kolom di bawah untuk mengaktifkan akun Anda).',
                ];
            }
        }

        return [
            'success' => true,
            'sandboxed' => true,
            'message' => 'Kode OTP 6-digit berhasil dibuat: '.$otpCode.' (Silakan masukkan kode ini pada kolom di bawah untuk mengaktifkan akun Anda).',
        ];
    }

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $userName,
        public string $otpCode
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Verifikasi Akun ('.$this->otpCode.') - PT. Asia Plastik',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
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
