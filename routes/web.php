<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = \App\Models\User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});

Route::get('/login', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = \App\Models\User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = \App\Models\User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = \App\Models\User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::get('/reset-session', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $response = redirect()->route('login');

    $cookiesToForget = [
        'corporate_ticketing_session',
        'corporate-ticketing-session',
        'laravel_session',
        'laravel-session',
        'XSRF-TOKEN',
    ];

    foreach ($cookiesToForget as $cookie) {
        $response->withCookie(cookie()->forget($cookie, '/', null));
    }

    return $response;
})->name('reset.session');

Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/'.$path);
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');

Route::get('/set-locale/{locale}', function (string $locale) {
    $allowed = \App\Http\Middleware\SetLocale::SUPPORTED_LOCALES;
    if (! in_array($locale, $allowed, true)) {
        $locale = 'id';
    }

    if (request()->hasSession()) {
        session(['locale' => $locale]);
    }

    $cookie = cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false);

    $referer = request()->header('referer');
    if ($referer && str_starts_with($referer, request()->getSchemeAndHttpHost())) {
        return redirect($referer)->withCookie($cookie);
    }

    return redirect()->route('dashboard')->withCookie($cookie);
})->name('locale.switch');

// ========================================================
// PASSWORD CONFIRMATION (Required for password.confirm middleware)
// ========================================================
Route::get('/confirm-password', function () {
    return view('auth.confirm-password');
})->middleware('auth')->name('password.confirm');

Route::post('/confirm-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'password' => ['required', 'string'],
    ]);

    if (! \Illuminate\Support\Facades\Hash::check($request->password, $request->user()->password)) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'password' => ['Password yang Anda masukkan tidak cocok dengan sistem.'],
        ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended(route('hcm.employees.master'));
})->middleware('auth')->name('password.confirm.store');

// ========================================================
// MODULE 3: THE VAULT - SECURE HRD MASTER DATA DASHBOARD
// Route: /hcm-core/employees-master (DO NOT use 'admin')
// Middleware: ['auth', 'password.confirm', EnsureHrDepartment]
// ========================================================
Route::get('/hcm-core/employees-master', function () {
    return view('hcm.employees');
})->middleware([
    'auth',
    'password.confirm',
    \App\Http\Middleware\EnsureHrDepartment::class,
])->name('hcm.employees.master');

