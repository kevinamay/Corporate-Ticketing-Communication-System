<?php

namespace App\Livewire;

use App\Mail\SendOtpMail;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
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
    public ?string $generatedOtpDemo = null;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'national_id_ktp' => 'required|string|min:16|max:16|regex:/^[0-9]+$/|unique:users,national_id_ktp',
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
            'national_id_ktp.required' => 'Nomor KTP wajib diisi.',
            'national_id_ktp.min' => 'Nomor KTP harus terdiri dari 16 digit.',
            'national_id_ktp.max' => 'Nomor KTP harus terdiri dari 16 digit.',
            'national_id_ktp.unique' => 'Nomor KTP ini sudah terdaftar di sistem.',
            'department_id.required' => 'Silakan pilih departemen yang sesuai.',
            'whatsapp_number.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.unique' => 'Email ini telah digunakan oleh akun lain.',
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
        $this->validate();

        // 1. Generate 6-digit random secure OTP code
        $otpCode = (string) random_int(100000, 999999);

        // 2. Handle optional avatar upload or generate UI avatar
        $avatarUrl = null;
        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $avatarUrl = '/storage/'.$path;
        } else {
            $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0284c7&color=fff';
        }

        // 3. Exact User::create Eloquent statement storing ALL specified fields
        $user = User::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
            'national_id_ktp' => trim($this->national_id_ktp),
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

        // 4. Kirim email OTP ke alamat Gmail / email yang dimasukkan
        try {
            Mail::to($user->email)->send(new SendOtpMail($user->name, $otpCode));
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email OTP: '.$e->getMessage());
        }

        $this->userId = $user->id;
        $this->generatedOtpDemo = $otpCode;
        $this->step = 2; // Transition to OTP Verification UI
        $this->errorMessage = null;
        $this->successMessage = 'Kode verifikasi 6-digit telah dikirimkan ke WhatsApp & Email Anda.';
    }

    public function verifyOtp(): void
    {
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
    }

    public function resendOtp(): void
    {
        if (! $this->userId) {
            return;
        }

        $user = User::find($this->userId);
        if ($user) {
            $newOtp = (string) random_int(100000, 999999);
            $user->update(['otp_code' => $newOtp]);
            $this->generatedOtpDemo = $newOtp;
            $this->reset(['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6']);
            $this->errorMessage = null;

            try {
                Mail::to($user->email)->send(new SendOtpMail($user->name, $newOtp));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim ulang email OTP: '.$e->getMessage());
            }

            $this->successMessage = 'Kode OTP baru berhasil dibuat dan dikirimkan.';
        }
    }

    public function render()
    {
        return view('livewire.register-user', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
