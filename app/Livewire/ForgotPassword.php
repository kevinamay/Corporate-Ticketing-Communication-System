<?php

namespace App\Livewire;

use App\Mail\SendPasswordResetMail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    public ?string $errorMessage = null;

    public ?string $resetUrl = null;

    public bool $isSent = false;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|email|max:150',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ];
    }

    public function sendResetLink(): void
    {
        $this->errorMessage = null;
        $this->statusMessage = null;
        $this->validate();

        $cleanEmail = strtolower(trim($this->email));

        $user = User::where('email', $cleanEmail)->first();

        if (! $user) {
            $this->errorMessage = 'Alamat email ini tidak terdaftar dalam sistem kami. Silakan periksa kembali.';

            return;
        }

        // Generate secure 64-character token
        $token = Str::random(64);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $cleanEmail],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $currentHost = request()->getHost();
        $isLocal = in_array($currentHost, ['127.0.0.1', 'localhost'], true) && ! env('VERCEL') && ! app()->environment('production');

        if ($isLocal) {
            $scheme = request()->isSecure() ? 'https' : 'http';
            $port = request()->getPort() ? ':'.request()->getPort() : '';
            $baseUrl = "{$scheme}://{$currentHost}{$port}";
        } else {
            $cloudHost = ! empty($currentHost) && ! in_array($currentHost, ['127.0.0.1', 'localhost'], true)
                ? $currentHost
                : 'ticketing-kappa-jet.vercel.app';
            $baseUrl = "https://{$cloudHost}";
        }

        $resetUrl = rtrim($baseUrl, '/').'/reset-password/'.$token.'?email='.urlencode($cleanEmail);

        $this->resetUrl = $resetUrl;
        $this->isSent = true;
        $this->statusMessage = 'Tautan reset password telah berhasil dibuat dan dikirimkan.';

        // Safe non-blocking email dispatch attempt
        try {
            SendPasswordResetMail::sendTo($cleanEmail, $user->name, $resetUrl);
        } catch (\Throwable $e) {
            Log::info('Pengiriman email reset password dilewati: '.$e->getMessage());
        }

        session()->flash('status', 'Tautan reset password berhasil dibuat untuk email '.$cleanEmail.'. Silakan masukkan password baru Anda di bawah ini.');

        $this->redirect($resetUrl, navigate: false);
    }

    public function resend(): void
    {
        $this->sendResetLink();
    }

    public function render()
    {
        return view('livewire.forgot-password');
    }
}
