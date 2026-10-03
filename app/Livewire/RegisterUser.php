<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RegisterUser extends Component
{
    // Registration Form Inputs
    public string $name = '';

    public string $whatsapp_number = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    // Registration outcome state
    public bool $isRegisteredSuccess = false;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'whatsapp_number' => 'required|string|min:10|max:20|regex:/^[0-9+ ]+$/',
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
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'whatsapp_number.required' => 'Nomor WhatsApp atau Telfon aktif wajib diisi.',
            'whatsapp_number.min' => 'Nomor WhatsApp atau Telfon minimal 10 digit.',
            'whatsapp_number.regex' => 'Format nomor WhatsApp atau Telfon tidak valid.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    public function register(): void
    {
        $this->errorMessage = null;
        $this->validate();

        try {
            $cleanEmail = strtolower(trim($this->email));

            // Check if active (already approved) user exists with this email
            $existingUser = User::where('email', $cleanEmail)->first();
            if ($existingUser && $existingUser->email_verified_at !== null) {
                $this->addError('email', 'Email ini telah terdaftar dan akun sudah aktif. Silakan masuk melalui halaman login.');

                return;
            }

            // Generate UI Avatar based on name
            $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0284c7&color=fff';

            if ($existingUser) {
                $existingUser->update([
                    'name' => trim($this->name),
                    'email' => $cleanEmail,
                    'password' => $this->password,
                    'whatsapp_number' => trim($this->whatsapp_number),
                    'department_id' => $existingUser->department_id ?: null,
                    'avatar' => $avatarUrl,
                    'otp_code' => null,
                    'email_verified_at' => null, // Remains unverified/unapproved until Admin confirms
                ]);
            } else {
                User::create([
                    'name' => trim($this->name),
                    'email' => $cleanEmail,
                    'password' => $this->password,
                    'whatsapp_number' => trim($this->whatsapp_number),
                    'department_id' => null,
                    'avatar' => $avatarUrl,
                    'otp_code' => null,
                    'email_verified_at' => null, // Waiting for Admin ACC / Confirmation
                    'role' => 'staff',
                ]);
            }

            $this->isRegisteredSuccess = true;
            $this->errorMessage = null;
            $this->successMessage = 'Pendaftaran berhasil dikirim. Akun Anda saat ini sedang menunggu ACC/Konfirmasi dari Administrator.';
            $this->dispatch('userRegistered');
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('Registrasi gagal: '.$e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan sistem saat mendaftar: '.$e->getMessage();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'whatsapp_number', 'email', 'password', 'password_confirmation', 'isRegisteredSuccess', 'errorMessage', 'successMessage']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.register-user');
    }
}
