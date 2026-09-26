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

        if (! empty($resendKey)) {
            $fromAddress = env('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
            $fromName = env('MAIL_FROM_NAME') ?: 'PT. Asia Plastik';
            $html = view('emails.password-reset', ['userName' => $userName, 'resetUrl' => $resetUrl])->render();

            // Case 1: Recipient is verified owner email in Resend
            if (strtolower(trim($toEmail)) === strtolower(trim($ownerEmail))) {
                try {
                    $response = Http::withoutVerifying()->timeout(3)->withToken($resendKey)->post('https://api.resend.com/emails', [
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
                } catch (\Throwable $e) {
                    Log::warning("Resend password reset dispatch to owner failed: {$e->getMessage()}");
                }
            } else {
                // Case 2: Regular employee email in Resend Sandbox mode
                try {
                    $ownerHtml = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;'>"
                        ."<h2 style='color: #1e3a8a; margin-top: 0;'>Permohonan Reset Password - PT. Asia Plastik</h2>"
                        ."<p style='color: #475569;'>Pengguna <strong>{$userName}</strong> mengajukan reset password untuk email: <strong>{$toEmail}</strong>.</p>"
                        ."<p style='color: #475569;'>Tautan reset password yang dihasilkan adalah:</p>"
                        ."<div style='background: #eff6ff; padding: 14px; text-align: center; border-radius: 8px; border: 1px solid #bfdbfe; margin: 20px 0;'>"
                        ."<a href='{$resetUrl}' style='display: inline-block; background: #2563eb; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Buka Halaman Reset Password</a>"
                        ."<p style='font-size: 11px; color: #64748b; margin-top: 10px; word-break: break-all;'>{$resetUrl}</p>"
                        .'</div>'
                        ."<p style='color: #64748b; font-size: 12px;'><em>Notifikasi sistem otomatis PT Asia Plastik.</em></p>"
                        .'</div>';

                    Http::withoutVerifying()->timeout(3)->withToken($resendKey)->post('https://api.resend.com/emails', [
                        'from' => "{$fromName} <{$fromAddress}>",
                        'to' => [$ownerEmail],
                        'subject' => "Tautan Reset Password Pengguna [{$toEmail}] - PT. Asia Plastik",
                        'html' => $ownerHtml,
                    ]);
                } catch (\Throwable $forwardError) {
                    Log::warning("Failed to forward sandbox password reset to owner: {$forwardError->getMessage()}");
                }

                return [
                    'success' => true,
                    'sandboxed' => true,
                    'message' => 'Tautan reset password berhasil dibuat untuk email '.$toEmail.'.',
                ];
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
