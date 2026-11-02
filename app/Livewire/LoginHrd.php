<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class LoginHrd extends Component
{
    public string $login_id = '';

    public string $password = '';

    public bool $remember = false;

    public ?string $errorMessage = null;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'login_id' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'login_id.required' => __('Email atau Nomor WhatsApp staf HRD wajib diisi.'),
            'password.required' => __('Password wajib diisi.'),
        ];
    }

    public function login()
    {
        $this->errorMessage = null;
        $this->validate();

        $input = trim($this->login_id);
        $cleanEmail = strtolower($input);

        $authenticated = false;

        // 1. Coba Login dengan Email
        if (Auth::attempt(['email' => $cleanEmail, 'password' => $this->password], $this->remember)
            || Auth::attempt(['email' => $input, 'password' => $this->password], $this->remember)) {
            $authenticated = true;
        }

        // 2. Coba Login dengan WhatsApp
        $phoneVariations = [$input];
        $digitsOnly = preg_replace('/[^0-9]/', '', $input);
        if (! empty($digitsOnly)) {
            $phoneVariations[] = $digitsOnly;
            if (str_starts_with($digitsOnly, '62')) {
                $phoneVariations[] = '0'.substr($digitsOnly, 2);
            } elseif (str_starts_with($digitsOnly, '0')) {
                $phoneVariations[] = '62'.substr($digitsOnly, 1);
                $phoneVariations[] = '+62'.substr($digitsOnly, 1);
            }
        }
        $phoneVariations = array_values(array_unique($phoneVariations));

        if (! $authenticated) {
            foreach ($phoneVariations as $phone) {
                if (Auth::attempt(['whatsapp_number' => $phone, 'password' => $this->password], $this->remember)) {
                    $authenticated = true;
                    break;
                }
            }
        }

        if ($authenticated) {
            $user = Auth::user();

            // 1. Validasi ACC / Verifikasi Admin
            if ($user && $user->email_verified_at === null) {
                Auth::logout();
                session()->forget('active_user_id');
                $this->errorMessage = 'Akun Anda belum aktif karena masih menunggu persetujuan (ACC/Konfirmasi) dari Administrator. Silakan hubungi Administrator IT untuk aktivasi akun.';

                return;
            }

            // 2. Validasi Otorisasi Khusus HRD / Admin IT
            $isAuthorizedHrd = false;
            if ($user->role === 'admin' || $user->email === 'user123@gmail.com') {
                $isAuthorizedHrd = true;
            } else {
                $deptName = strtolower($user->department?->name ?? '');
                if (str_contains($deptName, 'hr') || str_contains($deptName, 'human resource') || str_contains($deptName, 'personalia')) {
                    $isAuthorizedHrd = true;
                }
            }

            if (! $isAuthorizedHrd) {
                Auth::logout();
                session()->forget('active_user_id');
                $this->errorMessage = 'Akses Terbatas: Akun Anda terdaftar di divisi lain. Halaman ini khusus diperuntukkan bagi Tim Human Resources (HRD) dan Administrator. Silakan gunakan Portal Karyawan.';

                return;
            }

            session()->regenerate();
            session(['active_user_id' => Auth::id()]);
            session(['auth.password_confirmed_at' => time()]);

            // Arahkan ke dashboard atau langsung modul data karyawan jika admin
            if ($user->email === 'user123@gmail.com') {
                $this->redirect(route('hcm.employees.master'), navigate: false);
            } else {
                $this->redirect(route('dashboard'), navigate: false);
            }

            return;
        }

        // Tanggapan diagnostik kredensial gagal
        $existing = User::where('email', $cleanEmail)
            ->orWhere('email', $input)
            ->orWhere(function ($query) use ($phoneVariations) {
                $query->whereIn('whatsapp_number', $phoneVariations);
            })
            ->first();

        if (! $existing) {
            $this->errorMessage = 'Akun HRD dengan Email atau No. WhatsApp tersebut belum terdaftar di sistem korporat.';
        } elseif (! Hash::check($this->password, $existing->password)) {
            $this->errorMessage = 'Password yang Anda masukkan salah. Silakan periksa kembali.';
        } elseif ($existing->email_verified_at === null) {
            $this->errorMessage = 'Akun Anda belum aktif karena masih menunggu persetujuan (ACC) dari Administrator.';
        } else {
            $this->errorMessage = 'Kredensial tidak cocok dengan data staf HRD kami. Silakan periksa kembali Email/No. WhatsApp dan Password Anda.';
        }
    }

    public function render()
    {
        return view('livewire.login-hrd');
    }
}
