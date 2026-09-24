<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.register');
})->name('register');

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

    $response = redirect()->route('dashboard');

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
