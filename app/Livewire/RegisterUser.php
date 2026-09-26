<?php

namespace App\Livewire;

use App\Mail\SendOtpMail;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RegisterUser extends Component
{
    // Step state: 1 = Form, 2 = OTP Verification
    public int $step = 1;

    public ?int $userId = null;

    // Registration Form Inputs (Only 5 inputs requested)
    public string $name = '';

    public string $whatsapp_number = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    // 6 OTP Input Digits
    public string $otp1 = '';

    public string $otp2 = '';

    public string $otp3 = '';

    public string $otp4 = '';

    public string $otp5 = '';

    public string $otp6 = '';

    // Helpers
    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public ?string $generatedOtp = null;

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

            // Check if verified user already exists with this email
            $existingUser = User::where('email', $cleanEmail)->first();
            if ($existingUser && $existingUser->email_verified_at !== null) {
                $this->addError('email', 'Email ini telah terdaftar dan aktif. Silakan masuk melalui halaman login.');

                return;
            }

            // Generate genuinely random 6-digit secure OTP code
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Generate UI Avatar based on name
            $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0284c7&color=fff';

            // Assign default department
            $defaultDeptId = Department::first()?->id;

            if ($existingUser) {
                $existingUser->update([
                    'name' => trim($this->name),
                    'email' => $cleanEmail,
                    'password' => $this->password,
                    'whatsapp_number' => trim($this->whatsapp_number),
                    'department_id' => $existingUser->department_id ?: $defaultDeptId,
                    'avatar' => $avatarUrl,
                    'otp_code' => $otpCode,
                    'email_verified_at' => null,
                ]);
                $user = $existingUser;
            } else {
                $user = User::create([
                    'name' => trim($this->name),
                    'email' => $cleanEmail,
                    'password' => $this->password,
                    'whatsapp_number' => trim($this->whatsapp_number),
                    'department_id' => $defaultDeptId,
                    'avatar' => $avatarUrl,
                    'otp_code' => $otpCode,
                    'email_verified_at' => null,
                    'role' => 'staff',
                ]);
            }

            $this->userId = $user->id;
            $this->generatedOtp = $otpCode;
            $this->step = 2; // Transition to OTP Verification UI immediately
            $this->errorMessage = null;
            $this->successMessage = 'Kode verifikasi OTP berhasil dibuat. Silakan masukkan 6 digit kode di bawah.';

            // Fast non-blocking email dispatch attempt
            try {
                SendOtpMail::sendTo($user->email, $user->name, $otpCode);
            } catch (\Throwable $e) {
                Log::info('Pengiriman email OTP dilewati: '.$e->getMessage());
            }
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('Registrasi gagal: '.$e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan sistem saat mendaftar: '.$e->getMessage();
        }
    }

    public function verifyOtp(): void
    {
        try {
            $this->errorMessage = null;

            $enteredOtp = trim($this->otp1.$this->otp2.$this->otp3.$this->otp4.$this->otp5.$this->otp6);

            if (strlen($enteredOtp) !== 6) {
                $this->errorMessage = 'Silakan masukkan 6 digit kode OTP secara lengkap.';

                return;
            }

            $user = User::find($this->userId);

            if (! $user) {
                $this->errorMessage = 'Data pengguna tidak ditemukan. Silakan registrasi ulang.';
                $this->step = 1;

                return;
            }

            if ($user->otp_code !== $enteredOtp) {
                $this->errorMessage = 'Kode OTP tidak cocok atau tidak valid. Silakan periksa kembali.';

                return;
            }

            // OTP Verified successfully
            $user->update([
                'email_verified_at' => now(),
                'otp_code' => null,
            ]);

            // Sesuai alur: setelah verifikasi OTP selesai, arahkan kembali ke halaman login
            Auth::logout();
            session()->forget('active_user_id');

            session()->flash('status', 'Pendaftaran & verifikasi OTP berhasil! Silakan masuk menggunakan Email dan Password Anda.');
            $this->redirect(route('login'), navigate: false);
        } catch (\Throwable $e) {
            Log::error('Verifikasi OTP gagal: '.$e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan saat memverifikasi OTP: '.$e->getMessage();
        }
    }

    public function resendOtp(): void
    {
        if (! $this->userId) {
            $this->errorMessage = 'Sesi pendaftaran tidak ditemukan. Silakan isi form kembali.';
            $this->step = 1;

            return;
        }

        $user = User::find($this->userId);
        if (! $user) {
            $this->errorMessage = 'Pengguna tidak ditemukan. Silakan isi form kembali.';
            $this->step = 1;

            return;
        }

        $newOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['otp_code' => $newOtp]);
        $this->generatedOtp = $newOtp;
        $this->successMessage = 'Kode OTP baru berhasil dibuat. Masukkan kode 6 digit di bawah ini.';
        $this->errorMessage = null;

        try {
            SendOtpMail::sendTo($user->email, $user->name, $newOtp);
        } catch (\Throwable $e) {
            Log::info('Pengiriman email OTP baru dilewati: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.register-user');
    }
}
