<div class="min-h-screen relative flex items-center justify-center p-3 sm:p-6 lg:p-8 bg-gradient-to-br from-sky-100 via-blue-50 to-white dark:from-slate-950 dark:via-slate-900 dark:to-sky-950 overflow-y-auto transition-colors duration-300">
    
    <!-- AI Slop Light & Airy Soft Glowing Orbs (Light Blue & White Palette with Dark Mode Support) -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-200/60 dark:bg-sky-500/20 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-200/50 dark:bg-blue-600/20 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-duration: 6s;"></div>
    <div class="absolute top-1/3 right-1/4 w-80 h-80 bg-cyan-100/70 dark:bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 left-1/4 w-72 h-72 bg-indigo-100/50 dark:bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Auth Card: Clean Crisp Glassmorphism in Light Mode & Deep Slate/Sky Glass in Dark Mode -->
    <div class="relative z-10 w-full max-w-md bg-white/90 dark:bg-slate-900/90 shadow-[0_20px_60px_-15px_rgba(14,165,233,0.25)] dark:shadow-[0_20px_60px_-15px_rgba(2,132,199,0.3)] border border-sky-200/80 dark:border-sky-800/60 rounded-2xl overflow-hidden my-4 sm:my-8 transition-all backdrop-blur-xl">
        
        <!-- Top Bar: Sky to Royal Blue Gradient -->
        <div class="bg-gradient-to-r from-sky-600 via-blue-600 to-sky-700 dark:from-sky-900 dark:via-blue-900 dark:to-slate-900 px-4 sm:px-6 py-3.5 sm:py-4 flex items-center justify-between text-white border-b border-sky-400/40 dark:border-sky-700/50 relative overflow-hidden shadow-sm">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white/20 dark:from-sky-400/10 via-transparent to-transparent pointer-events-none"></div>
            <a href="{{ url('/') }}" class="flex items-center group relative z-10">
                <img src="{{ asset('images/logo2.webp') }}" alt="PT. Asia Plastik" class="h-9 sm:h-11 w-auto object-contain transition duration-200 group-hover:opacity-95 drop-shadow-sm">
            </a>
            <div class="text-right relative z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold bg-white/20 dark:bg-sky-950/60 text-white border border-white/30 dark:border-sky-500/30 shadow-sm backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-cyan-300 animate-ping"></span>
                    <svg class="w-3.5 h-3.5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    {{ __('Portal HRD • AI Slop') }}
                </span>
            </div>
        </div>

        <!-- Login Form Body -->
        <div class="p-5 sm:p-7 md:p-8 bg-white/95 dark:bg-slate-900/95 transition-colors duration-200">
            <div class="mb-6 text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200/80 dark:border-sky-800 mb-2 shadow-sm">
                    <svg class="w-3 h-3 text-sky-500 dark:text-sky-400 animate-spin" style="animation-duration: 6s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('AI Human Capital Core') }}
                </div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center justify-center gap-2">
                    <span>{{ __('Selamat Datang, Tim HRD') }}</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Autentikasi Terpusat Divisi Human Resources & Personalia') }}</p>
            </div>

            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errorMessage)
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-4">
                
                <!-- Input: Email or WhatsApp HRD -->
                <div>
                    <label for="hrd_login_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ __('Email / No. WhatsApp HRD') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sky-500 dark:text-sky-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" id="hrd_login_id" wire:model="login_id" placeholder="{{ __('hrd@perusahaan.com atau No. WhatsApp') }}"
                               class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-xl border @error('login_id') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-sky-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-sky-500 dark:focus:border-sky-400 focus:ring-2 focus:ring-sky-200 dark:focus:ring-sky-900/50 outline-none transition shadow-sm">
                    </div>
                    @error('login_id') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Input: Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="hrd_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            {{ __('Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-[11px] text-sky-600 dark:text-sky-400 hover:text-sky-800 dark:hover:text-sky-300 transition font-medium">{{ __('Lupa password?') }}</a>
                    </div>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sky-500 dark:text-sky-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" id="hrd_password" wire:model="password" placeholder="{{ __('Masukkan password staf HRD') }}"
                               class="w-full text-xs pl-9 pr-10 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-sky-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-sky-500 dark:focus:border-sky-400 focus:ring-2 focus:ring-sky-200 dark:focus:ring-sky-900/50 outline-none transition shadow-sm">
                        <button type="button" 
                                @click="show = !show"
                                tabindex="-1"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none cursor-pointer"
                                :title="show ? '{{ __('Sembunyikan password') }}' : '{{ __('Tampilkan password') }}'">
                            <span x-show="!show" class="flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                            <span x-show="show" class="flex items-center" style="display: none;">
                                <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    @error('password') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between py-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-300">
                        <input type="checkbox" wire:model="remember" class="rounded border-sky-300 dark:border-slate-700 text-sky-600 focus:ring-sky-500">
                        <span>{{ __('Ingat sesi saya di portal ini') }}</span>
                    </label>
                </div>

                <!-- Submit Button with Light Blue to Royal Blue AI Gradient -->
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3 px-4 bg-gradient-to-r from-sky-500 via-blue-600 to-sky-600 hover:from-sky-400 hover:via-blue-500 hover:to-sky-500 disabled:opacity-50 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ __('Masuk ke Portal HRD') }}
                    </span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        {{ __('Memverifikasi Kredensial AI...') }}
                    </span>
                </button>

            </form>

            <!-- Switch to Employee Portal Link -->
            <div class="mt-6 pt-5 border-t border-sky-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                {{ __('Bukan staf HRD atau Admin?') }}
                <a href="{{ route('login') }}" class="font-bold text-sky-600 dark:text-sky-400 hover:text-sky-800 dark:hover:text-sky-300 transition underline ml-1">
                    {{ __('Masuk via Portal Karyawan') }}
                </a>
            </div>

        </div>

        <!-- AI Footer: Clean White & Light Sky with Dark Mode Support -->
        <div class="bg-sky-50/80 dark:bg-slate-950/80 px-4 sm:px-6 py-2.5 sm:py-3 border-t border-sky-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 backdrop-blur-md">
            <span class="font-semibold text-slate-600 dark:text-slate-300">&copy; {{ date('Y') }} PT. Asia Plastik</span>
            <span class="font-semibold text-sky-700 dark:text-sky-400 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 dark:bg-sky-400 inline-block animate-pulse"></span>
                {{ __('HCM Core & AI Intelligence Console') }}
            </span>
        </div>

    </div>
</div>
