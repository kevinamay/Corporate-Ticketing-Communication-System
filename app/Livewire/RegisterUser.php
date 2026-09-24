<?php

namespace App\Livewire;

use App\Mail\SendOtpMail;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class RegisterUser extends Component
{
    use WithFileUploads;

    // Step state: 1 = Form, 2 = OTP Verification
    public int $step = 1;

    public ?int $userId = null;

    // Registration Form Inputs
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $national_id_ktp = '';

    public string $gender = 'male';

    public string $whatsapp_number = '';

    public string $complete_address = '';

    public string $postal_code = '';

    public ?int $department_id = null;

    public $avatar = null;

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

    /**
     * @return array<string, string>
     */
    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:8|confirmed',
            'national_id_ktp' => 'required|string|min:16|max:16|regex:/^[0-9]+$/',
            'gender' => 'required|in:male,female',
            'whatsapp_number' => 'required|string|min:10|max:20|regex:/^[0-9+ ]+$/',
            'complete_address' => 'required|string|min:10|max:500',
            'postal_code' => 'required|string|min:5|max:10|regex:/^[0-9]+$/',
            'department_id' => 'required|exists:departments,id',
            'avatar' => 'nullable|image|max:10240',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'national_id_ktp.required' => 'Nomor KTP wajib diisi.',
            'national_id_ktp.min' => 'Nomor KTP harus terdiri dari 16 digit.',
            'national_id_ktp.max' => 'Nomor KTP harus terdiri dari 16 digit.',
            'national_id_ktp.regex' => 'Nomor KTP harus berupa 16 angka.',
            'department_id.required' => 'Silakan pilih departemen yang sesuai.',
            'whatsapp_number.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'avatar.max' => 'Ukuran foto profil tidak boleh lebih dari 10MB.',
            'avatar.image' => 'File harus berupa foto/gambar (JPG, JPEG, PNG, WEBP).',
        ];
    }

    public function mount(): void
    {
        $firstDept = Department::first();
        if ($firstDept) {
            $this->department_id = $firstDept->id;
        }
    }

    public function register(): void
    {
        $this->errorMessage = null;

        // 1. Sanitize avatar on serverless if temporary file was purged or on another worker
        if ($this->avatar) {
            try {
                if (! ($this->avatar instanceof TemporaryUploadedFile) || ! $this->avatar->exists()) {
                    $this->avatar = null;
                }
            } catch (\Throwable $e) {
                Log::warning('Temporary avatar check fallback: '.$e->getMessage());
                $this->avatar = null;
            }
        }

        // Validate basic rules
        $this->validate();

        try {
            $cleanEmail = strtolower(trim($this->email));
            $cleanKtp = trim($this->national_id_ktp);

            // 2. Check if verified user already exists with email or KTP
            $userByEmail = User::where('email', $cleanEmail)->first();
            if ($userByEmail && $userByEmail->email_verified_at !== null) {
                $this->addError('email', 'Email ini telah terdaftar dan aktif. Silakan masuk melalui halaman login.');

                return;
            }

            $userByKtp = User::where('national_id_ktp', $cleanKtp)->first();
            if ($userByKtp && $userByKtp->email_verified_at !== null) {
                $this->addError('national_id_ktp', 'Nomor KTP ini telah terdaftar dan aktif. Silakan masuk melalui halaman login.');

                return;
            }

            // 3. Generate genuinely random 6-digit secure OTP code
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // 4. Handle optional avatar upload or generate UI avatar
            $avatarUrl = null;
            if ($this->avatar) {
                try {
                    $path = $this->avatar->store('avatars', 'public');
                    $avatarUrl = '/storage/'.$path;
                } catch (\Throwable $e) {
                    Log::warning('Avatar store fallback: '.$e->getMessage());
                    $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0284c7&color=fff';
                }
            } else {
                $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0284c7&color=fff';
            }

            // 5. Create or update unverified user
            $targetUser = $userByEmail ?: $userByKtp;
            if ($targetUser) {
                $targetUser->update([
                    'name' => trim($this->name),
                    'email' => $cleanEmail,
                    'password' => $this->password,
                    'national_id_ktp' => $cleanKtp,
                    'gender' => $this->gender,
                    'whatsapp_number' => trim($this->whatsapp_number),
                    'complete_address' => trim($this->complete_address),
                    'postal_code' => trim($this->postal_code),
                    'department_id' => $this->department_id,
                    'avatar' => $avatarUrl,
                    'otp_code' => $otpCode,
                    'email_verified_at' => null,
                ]);
                $user = $targetUser;
            } else {
                $user = User::create([
                    'name' => trim($this->name),
                    'email' => $cleanEmail,
                    'password' => $this->password,
                    'national_id_ktp' => $cleanKtp,
                    'gender' => $this->gender,
                    'whatsapp_number' => trim($this->whatsapp_number),
                    'complete_address' => trim($this->complete_address),
                    'postal_code' => trim($this->postal_code),
                    'department_id' => $this->department_id,
                    'avatar' => $avatarUrl,
                    'otp_code' => $otpCode,
                    'email_verified_at' => null,
                    'role' => 'staff',
                ]);
            }

            $this->userId = $user->id;
            $this->step = 2; // Transition to OTP Verification UI
            $this->errorMessage = null;

            // 6. Kirim email OTP ke alamat email pendaftar
            try {
                SendOtpMail::sendTo($user->email, $user->name, $otpCode);
                $this->successMessage = 'Kode OTP 6-digit telah dikirim ke '.$user->email.'. Silakan periksa inbox atau folder spam email Anda.';
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email OTP: '.$e->getMessage());
                $this->errorMessage = 'Pendaftaran tersimpan, namun pengiriman email ke '.$user->email.' mengalami kendala: '.$e->getMessage().'. Silakan klik "Kirim Ulang OTP".';
            }
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('Registrasi gagal: '.$e->getMessage()."\n".$e->getTraceAsString());
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

            // Login user
            Auth::login($user);
            session(['active_user_id' => $user->id]);

            session()->flash('status', 'Registrasi dan verifikasi berhasil! Selamat datang di Portal Ticketing.');
            $this->redirect(route('dashboard'), navigate: true);
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

        try {
            $user = User::find($this->userId);
            if ($user) {
                $newOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->update(['otp_code' => $newOtp]);
                $this->reset(['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6']);
                $this->errorMessage = null;

                try {
                    SendOtpMail::sendTo($user->email, $user->name, $newOtp);
                    $this->successMessage = 'Kode OTP baru telah berhasil dikirimkan ke '.$user->email.'. Silakan periksa inbox atau folder spam Anda.';
                } catch (\Throwable $e) {
                    Log::error('Gagal mengirim ulang email OTP: '.$e->getMessage());
                    $this->errorMessage = 'Pengiriman ulang email ke '.$user->email.' mengalami kendala: '.$e->getMessage().'.';
                }
            } else {
                $this->errorMessage = 'Data pengguna tidak ditemukan. Silakan registrasi ulang.';
                $this->step = 1;
            }
        } catch (\Throwable $e) {
            Log::error('Resend OTP error: '.$e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan saat membuat kode OTP baru: '.$e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.register-user', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
