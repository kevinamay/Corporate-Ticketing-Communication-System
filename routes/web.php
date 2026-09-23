<?php

use Illuminate\Hashing\BcryptHasher;
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

Route::get('/diag-hash', function () {
    $info = [
        'hashing_config' => config('hashing'),
        'PASSWORD_BCRYPT' => PASSWORD_BCRYPT,
    ];

    foreach ([10, 12, '12', (int) env('BCRYPT_ROUNDS', 12)] as $cost) {
        $key = 'cost_'.var_export($cost, true);
        try {
            $info[$key] = password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
        } catch (Throwable $e) {
            $info[$key.'_error'] = get_class($e).': '.$e->getMessage();
        }
    }

    try {
        $hasher10 = new BcryptHasher(['rounds' => 10]);
        $info['hasher10'] = $hasher10->make('test');
    } catch (Throwable $e) {
        $info['hasher10_error'] = get_class($e).': '.$e->getMessage();
    }

    try {
        $hasherDefault = new BcryptHasher;
        $info['hasherDefault'] = $hasherDefault->make('test');
    } catch (Throwable $e) {
        $info['hasherDefault_error'] = get_class($e).': '.$e->getMessage();
    }

    try {
        $info['Hash_make'] = Hash::make('test');
    } catch (Throwable $e) {
        $info['Hash_make_error'] = get_class($e).': '.$e->getMessage();
    }

    return response()->json($info);
});
