<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
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
            'login_id.required' => 'Nomor KTP atau Alamat Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    public function login()
    {
        $this->errorMessage = null;
        $this->validate();

        $input = trim($this->login_id);

        // Detect whether input is an email or national_id_ktp
        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'national_id_ktp';

        $credentials = [
            $fieldType => $input,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            session(['active_user_id' => Auth::id()]);

            return redirect()->intended(route('dashboard'));
        }

        $this->errorMessage = 'Kredensial tidak cocok dengan data kami. Silakan periksa kembali Nomor KTP / Email dan Password Anda.';
    }

    public function render()
    {
        return view('livewire.login-user');
    }
}
