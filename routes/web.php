<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/'.$path);
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');

Route::get('/api/diag-hash', function () {
    $info = [
        'algos' => password_algos(),
        'PASSWORD_BCRYPT_defined' => defined('PASSWORD_BCRYPT'),
        'PASSWORD_DEFAULT' => PASSWORD_DEFAULT,
    ];
    try {
        $info['bcrypt_result'] = password_hash('test', PASSWORD_BCRYPT);
    } catch (Throwable $e) {
        $info['bcrypt_error'] = get_class($e).': '.$e->getMessage();
    }
    try {
        $info['default_result'] = password_hash('test', PASSWORD_DEFAULT);
    } catch (Throwable $e) {
        $info['default_error'] = get_class($e).': '.$e->getMessage();
    }
    try {
        $info['hash_make'] = Hash::make('test');
    } catch (Throwable $e) {
        $info['hash_make_error'] = get_class($e).': '.$e->getMessage();
    }

    return response()->json($info);
});
