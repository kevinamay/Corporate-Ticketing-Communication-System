<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $errorMessage = null;

    public bool $isTokenValid = true;

    public function mount(?string $token = null): void
    {
        $this->token = $token ?: (string) request()->route('token', '');
        $this->email = (string) request()->query('email', '');

        // If email was not passed in query string, resolve from token record
        if (empty($this->email) && ! empty($this->token)) {
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('token', $this->token)
                ->first();
            if ($tokenRecord) {
                $this->email = $tokenRecord->email;
            }
        }

        // Verify initial token validity
        if (! empty($this->token)) {
            $record = null;
            if (! empty($this->email)) {
                $record = DB::table('password_reset_tokens')
                    ->where('email', strtolower(trim($this->email)))
                    ->first();
            }
            if (! $record) {
                $record = DB::table('password_reset_tokens')
                    ->where('token', $this->token)
                    ->first();
                if ($record) {
                    $this->email = $record->email;
                }
            }

            if (! $record) {
                $this->isTokenValid = false;
                $this->errorMessage = 'Tautan reset password ini tidak valid atau sudah kadaluarsa. Silakan ajukan permohonan baru.';
            } else {
                $matches = ($record->token === $this->token) || Hash::check($this->token, $record->token);
                $isExpired = Carbon::parse($record->created_at)->addMinutes(60)->isPast();

                if (! $matches || $isExpired) {
                    $this->isTokenValid = false;
                    $this->errorMessage = $isExpired
                        ? 'Tautan reset password telah kadaluarsa (melewati batas 60 menit). Silakan ajukan permohonan baru.'
                        : 'Token keamanan reset password tidak cocok. Silakan ajukan permohonan baru.';
                }
            }
        }
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'token.required' => 'Token reset password tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
        ];
    }

    public function resetPassword(): void
    {
        $this->errorMessage = null;
        $this->validate();

        $cleanEmail = strtolower(trim($this->email));

        $user = User::where('email', $cleanEmail)->first();
        if (! $user) {
            $this->errorMessage = 'Pengguna dengan alamat email ini tidak ditemukan.';

            return;
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $cleanEmail)
            ->first();

        if (! $record) {
            $this->errorMessage = 'Tautan reset password tidak valid atau sudah pernah digunakan. Silakan ajukan baru.';

            return;
        }

        $matches = ($record->token === $this->token) || Hash::check($this->token, $record->token);
        if (! $matches) {
            $this->errorMessage = 'Token keamanan tidak valid atau tidak cocok. Silakan ajukan permohonan reset password baru.';

            return;
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            $this->errorMessage = 'Tautan reset password telah kadaluarsa (lebih dari 60 menit). Silakan ajukan baru.';

            return;
        }

        // Update user's password
        $user->update([
            'password' => $this->password,
        ]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $cleanEmail)->delete();

        // Invalidate current sessions
        Auth::logout();
        session()->forget('active_user_id');

        session()->flash('status', 'Password Anda berhasil direset! Silakan masuk menggunakan password baru Anda.');

        $this->redirect(route('login'), navigate: false);
    }

    public function render()
    {
        return view('livewire.reset-password');
    }
}
