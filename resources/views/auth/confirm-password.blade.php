@extends('layouts.auth')

@section('content')
<div class="min-h-screen relative flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-slate-900 overflow-y-auto"
     style="background-image: url('/images/fotopt.webp'); background-size: cover; background-position: center;">
    
    <!-- Dark Blue Corporate Overlay -->
    <div class="absolute inset-0 bg-blue-950/85 backdrop-blur-[2px]"></div>

    <!-- Main Confirmation Card -->
    <div class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden my-8 transition-colors">
        
        <!-- Corporate Top Bar -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 px-6 py-4 flex items-center justify-between text-white border-b border-blue-700/50">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('images/logo2.webp') }}" alt="PT. Asia Plastik" class="h-10 w-auto object-contain">
            </a>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-200 border border-amber-400/30">
                <svg class="w-3 h-3 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                {{ __('Area Terproteksi') }}
            </span>
        </div>

        <div class="p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 mx-auto flex items-center justify-center mb-3 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">{{ __('Konfirmasi Akses Keamanan') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ __('Halaman ini memerlukan verifikasi kredensial ulang demi menjaga kerahasiaan Master Data Karyawan (HCM Core).') }}
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('Masukkan Password Anda') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password" 
                           autofocus
                           placeholder="{{ __('Password login Anda') }}"
                           class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div class="pt-2 flex items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" 
                       class="w-1/2 py-2.5 px-3 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs text-center border border-slate-300 dark:border-slate-700 transition">
                        {{ __('Batal & Kembali') }}
                    </a>

                    <button type="submit" 
                            class="w-1/2 py-2.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider text-center shadow-md transition cursor-pointer">
                        {{ __('Lanjutkan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
