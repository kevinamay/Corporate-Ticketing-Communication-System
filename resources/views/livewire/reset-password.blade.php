<div class="min-h-screen relative flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-slate-900 overflow-y-auto"
     style="background-image: url('/images/fotopt_2.webp'); background-size: cover; background-position: center;">
    
    <!-- Dark Blue Overlay -->
    <div class="absolute inset-0 bg-blue-900/80 backdrop-blur-[2px]"></div>

    <!-- Main Auth Card -->
    <div class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl border border-gray-100 dark:border-slate-800 rounded-xl overflow-hidden my-8 transition-colors">
        
        <!-- Corporate Top Bar -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 px-6 py-4 flex items-center justify-between text-white border-b border-blue-700/50">
            <a href="{{ url('/') }}" class="flex items-center group">
                <img src="{{ asset('images/logo2.webp') }}" alt="PT. Asia Plastik" class="h-10 sm:h-11 w-auto object-contain transition duration-200 group-hover:opacity-90">
            </a>
            <div class="text-right">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-200 border border-emerald-400/30">
                    {{ __('Password Baru') }}
                </span>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8">
            <div class="mb-6 text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center mx-auto mb-3 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Buat Password Baru') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                    {{ __('Silakan buat password baru yang aman untuk akun Anda.') }}
                </p>
            </div>

            @if ($errorMessage)
                <div class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            @if (! $isTokenValid && empty($password))
                <div class="text-center py-4 space-y-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ __('Silakan minta tautan reset baru melalui halaman lupa password.') }}
                    </p>
                    <a href="{{ route('password.request') }}" class="inline-block py-2.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition">
                        {{ __('Minta Tautan Baru') }}
                    </a>
                </div>
            @else
                <form wire:submit.prevent="resetPassword" class="space-y-4">
                    
                    <input type="hidden" wire:model="token">

                    <!-- Input: Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Alamat Email Akun') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" id="email" wire:model="email" placeholder="{{ __('nama@perusahaan.com') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('email') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        @error('email') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input: Password Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Password Baru') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input type="password" id="password" wire:model="password" placeholder="{{ __('Minimal 8 karakter') }}" autofocus
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('password') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        @error('password') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input: Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Konfirmasi Password Baru') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" placeholder="{{ __('Ketik ulang password baru Anda') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('password_confirmation') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        @error('password_confirmation') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="resetPassword">{{ __('Simpan Password Baru & Masuk') }}</span>
                        <span wire:loading wire:target="resetPassword" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            {{ __('Menyimpan...') }}
                        </span>
                    </button>

                </form>

                <!-- Navigation link -->
                <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-600 dark:text-slate-400">
                    <a href="{{ route('login') }}" class="font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition">
                        &larr; {{ __('Batal dan Kembali ke Login') }}
                    </a>
                </div>
            @endif

        </div>

        <!-- Footer -->
        <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
            <span>&copy; {{ date('Y') }} PT. Asia Plastik</span>
            <span>{{ __('Keamanan Sistem & Akun') }}</span>
        </div>

    </div>
</div>
