<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class LoginUser extends Component
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
            'login_id.required' => 'Email atau Nomor WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    public function login()
    {
        $this->errorMessage = null;
        $this->validate();

        $input = trim($this->login_id);
        $cleanEmail = strtolower($input);

        $authenticated = false;

        // 1. Try Email (case-insensitive & raw)
        if (Auth::attempt(['email' => $cleanEmail, 'password' => $this->password], $this->remember)
            || Auth::attempt(['email' => $input, 'password' => $this->password], $this->remember)) {
            $authenticated = true;
        }

        // 2. Try WhatsApp / Phone Number (support exact or 08xx / 62xx / +62xx formats)
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
            session()->regenerate();
            session(['active_user_id' => Auth::id()]);
            session(['auth.password_confirmed_at' => time()]);

            $this->redirect(route('dashboard'), navigate: false);

            return;
        }

        // Provide specific diagnostic feedback
        $existing = User::where('email', $cleanEmail)
            ->orWhere('email', $input)
            ->orWhere(function ($query) use ($phoneVariations) {
                $query->whereIn('whatsapp_number', $phoneVariations);
            })
            ->first();

        if (! $existing) {
            $this->errorMessage = 'Akun dengan Email atau No. WhatsApp tersebut belum terdaftar. Silakan lakukan registrasi terlebih dahulu.';
        } elseif (! Hash::check($this->password, $existing->password)) {
            $this->errorMessage = 'Password yang Anda masukkan salah. Silakan periksa kembali.';
        } else {
            $this->errorMessage = 'Kredensial tidak cocok dengan data kami. Silakan periksa kembali Email/No. WhatsApp dan Password Anda.';
        }
    }

    public function render()
    {
        return view('livewire.login-user');
    }
}
