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
            <div class="text-right hidden sm:block">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/30 text-blue-200 border border-blue-400/30">
                    {{ __('Portal Masuk') }}
                </span>
            </div>
        </div>

        <!-- Login Form Body -->
        <div class="p-6 sm:p-8">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Selamat Datang') }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Masuk dengan Nomor KTP atau Alamat Email Perusahaan Anda.') }}</p>
            </div>

            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errorMessage)
                <div class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-4">
                
                <!-- Input: KTP or Email -->
                <div>
                    <label for="login_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ __('Nomor KTP atau Alamat Email') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" id="login_id" wire:model="login_id" placeholder="{{ __('16 digit KTP atau email@perusahaan.com') }}"
                               class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('login_id') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                    </div>
                    @error('login_id') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Input: Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            {{ __('Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <a href="#" class="text-[11px] text-blue-600 dark:text-blue-400 hover:text-blue-800 transition">{{ __('Lupa password?') }}</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" id="password" wire:model="password" placeholder="{{ __('Masukkan password Anda') }}"
                               class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('password') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                    </div>
                    @error('password') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between py-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-300">
                        <input type="checkbox" wire:model="remember" class="rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                        <span>{{ __('Ingat saya di perangkat ini') }}</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                    <span wire:loading.remove wire:target="login">{{ __('Masuk ke Sistem') }}</span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        {{ __('Memverifikasi...') }}
                    </span>
                </button>

            </form>

            <!-- Link to Register -->
            <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-600 dark:text-slate-400">
                {{ __('Belum memiliki akun?') }}
                <a href="{{ route('register') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 transition underline ml-1">
                    {{ __('Daftar Akun Baru') }}
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
            <span>&copy; {{ date('Y') }} PT. Asia Plastik</span>
            <span class="font-medium text-slate-400 dark:text-slate-500">{{ __('Sistem Komunikasi & Tiket Internal') }}</span>
        </div>

    </div>
</div>
