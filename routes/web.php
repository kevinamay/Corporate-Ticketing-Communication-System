<?php

use App\Http\Middleware\EnsureHrDepartment;
use App\Http\Middleware\SetLocale;
use App\Mail\SendPasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});

Route::get('/debug-reset', function () {
    $report = [];
    $t0 = microtime(true);

    // 1. Database read
    try {
        $user = User::where('email', 'kevinamay23@gmail.com')->first();
        $report['db_read'] = [
            'time' => round((microtime(true) - $t0) * 1000, 2).'ms',
            'found' => (bool) $user,
            'name' => $user?->name,
        ];
    } catch (Throwable $e) {
        $report['db_read_error'] = $e->getMessage();
    }

    // 2. Database write
    $t1 = microtime(true);
    try {
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'kevinamay23@gmail.com'],
            ['token' => 'debug-test-token-123', 'created_at' => now()]
        );
        $report['db_write'] = [
            'time' => round((microtime(true) - $t1) * 1000, 2).'ms',
        ];
    } catch (Throwable $e) {
        $report['db_write_error'] = $e->getMessage();
    }

    // 3. Email dispatch
    $t2 = microtime(true);
    try {
        $mailResult = SendPasswordResetMail::sendTo(
            'kevinamay23@gmail.com',
            $user?->name ?? 'User',
            'https://ticketing-kappa-jet.vercel.app/reset-password/debug-test-token-123'
        );
        $report['mail_dispatch'] = [
            'time' => round((microtime(true) - $t2) * 1000, 2).'ms',
            'result' => $mailResult,
        ];
    } catch (Throwable $e) {
        $report['mail_error'] = $e->getMessage();
    }

    $report['total_time'] = round((microtime(true) - $t0) * 1000, 2).'ms';

    return response()->json($report);
});

Route::get('/login', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = User::find(session('active_user_id'));
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
        $activeUser = User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function (string $token) {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = User::find(session('active_user_id'));
        if ($activeUser) {
            Auth::login($activeUser);
        }
    }

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::get('/dashboard', function () {
    $activeUser = Auth::user();
    if (! $activeUser && session('active_user_id')) {
        $activeUser = User::find(session('active_user_id'));
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
    // 1. Check in standard storage path
    $filePath = storage_path('app/public/'.$path);
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }

    // 2. Check in /tmp serverless storage
    $tmpPath = '/tmp/storage/app/public/'.$path;
    if (file_exists($tmpPath)) {
        return response()->file($tmpPath);
    }

    // 3. Check in public directory
    $publicPath = public_path('storage/'.$path);
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }

    // 4. Return clean SVG fallback instead of 404 error
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="200" viewBox="0 0 400 200">
        <rect width="100%" height="100%" fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>
        <circle cx="200" cy="80" r="28" fill="#e2e8f0"/>
        <path d="M190 70 L210 70 L200 85 Z" fill="#94a3b8"/>
        <text x="50%" y="130" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="bold" fill="#64748b" text-anchor="middle">Bukti Lampiran Foto</text>
        <text x="50%" y="150" font-family="system-ui, -apple-system, sans-serif" font-size="11" fill="#94a3b8" text-anchor="middle">Sesi file serverless telah diarsipkan</text>
    </svg>';

    return response($svg, 200, [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'no-cache, private',
    ]);
})->where('path', '.*')->name('storage.local');

Route::get('/set-locale/{locale}', function (string $locale) {
    $allowed = SetLocale::SUPPORTED_LOCALES;
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

Route::post('/confirm-password', function (Request $request) {
    $request->validate([
        'password' => ['required', 'string'],
    ]);

    if (! Hash::check($request->password, $request->user()->password)) {
        throw ValidationException::withMessages([
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
    EnsureHrDepartment::class,
])->name('hcm.employees.master');
