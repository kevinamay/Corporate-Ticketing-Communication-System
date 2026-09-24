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
     */
    public static function sendTo(string $toEmail, string $userName, string $otpCode): void
    {
        if (app()->environment('testing')) {
            Mail::to($toEmail)->send(new self($userName, $otpCode));

            return;
        }

        $resendKey = env('RESEND_API_KEY') ?: (str_starts_with((string) env('MAIL_PASSWORD'), 're_') ? env('MAIL_PASSWORD') : null);

        if (! empty($resendKey)) {
            $fromAddress = env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev');
            $fromName = env('MAIL_FROM_NAME', 'PT. Asia Plastik');
            $html = view('emails.otp', ['userName' => $userName, 'otpCode' => $otpCode])->render();

            $response = Http::timeout(10)->withToken($resendKey)->post('https://api.resend.com/emails', [
                'from' => "{$fromName} <{$fromAddress}>",
                'to' => [$toEmail],
                'subject' => "Kode OTP Verifikasi Akun ({$otpCode}) - PT. Asia Plastik",
                'html' => $html,
            ]);

            if ($response->successful()) {
                Log::info("OTP email successfully dispatched to {$toEmail} via Resend API (ID: {$response->json('id')})");

                return;
            }

            Log::warning("Resend API dispatch failed with status {$response->status()}: {$response->body()}, attempting fallback mailer");
        }

        Mail::to($toEmail)->send(new self($userName, $otpCode));
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
