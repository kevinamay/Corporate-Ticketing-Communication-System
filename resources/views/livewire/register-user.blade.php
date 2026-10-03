<div class="min-h-screen relative flex items-center justify-center p-3 sm:p-6 lg:p-8 bg-slate-900 overflow-y-auto"
     style="background-image: url('/images/fotopt_2.webp'); background-size: cover; background-position: center;">
    
    <!-- Dark Blue Overlay -->
    <div class="absolute inset-0 bg-blue-900/80 backdrop-blur-[2px]"></div>

    <!-- Main Auth Card -->
    <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 shadow-2xl border border-gray-100 dark:border-slate-800 rounded-xl overflow-hidden my-4 sm:my-8 transition-colors">
        
        <!-- Corporate Top Bar -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 px-4 sm:px-6 py-3.5 sm:py-4 flex items-center justify-between text-white border-b border-blue-700/50">
            <a href="{{ url('/') }}" class="flex items-center group">
                <img src="{{ asset('images/logo2.webp') }}" alt="PT. Asia Plastik" class="h-9 sm:h-11 w-auto object-contain transition duration-200 group-hover:opacity-90">
            </a>
            <div class="text-right hidden sm:block">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/30 text-blue-200 border border-blue-400/30">
                    {{ __('Pendaftaran Akun Karyawan') }}
                </span>
            </div>
        </div>

        @if (! $isRegisteredSuccess)
            <!-- ================= REGISTRATION FORM ================= -->
            <div class="p-4 sm:p-6 md:p-8">
                <div class="mb-6 text-center">
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Pendaftaran Akun Karyawan') }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Lengkapi data kredensial untuk mengakses sistem komunikasi & ticketing internal.') }}</p>
                </div>

                @if ($errorMessage)
                    <div class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2.5">
                        <svg class="w-4 h-4 flex-shrink-0 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $errorMessage }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="register" class="space-y-4">
                    
                    <!-- 1. Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Nama Lengkap') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input type="text" id="name" wire:model="name" placeholder="{{ __('Contoh: Budi Santoso') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('name') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        @error('name') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 2. Nomor Telfon atau WA -->
                    <div>
                        <label for="whatsapp_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Nomor Telfon atau WA') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <input type="tel" id="whatsapp_number" wire:model="whatsapp_number" placeholder="{{ __('Contoh: 081234567890') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('whatsapp_number') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition font-mono">
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ __('Nomor aktif untuk kontak dan notifikasi pengerjaan tiket.') }}</p>
                        @error('whatsapp_number') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 3. Email yang aktif -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Email yang Aktif') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" id="email" wire:model="email" placeholder="{{ __('nama@perusahaan.com') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('email') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ __('Alamat email aktif untuk identitas akun & sistem tiket perusahaan.') }}</p>
                        @error('email') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 4. Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password" wire:model="password" placeholder="{{ __('Minimal 8 karakter') }}"
                                   class="w-full text-xs pl-9 pr-10 py-2.5 rounded-lg border @error('password') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                            <button type="button" 
                                    @click="show = !show"
                                    tabindex="-1"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none cursor-pointer"
                                    :title="show ? '{{ __('Sembunyikan password') }}' : '{{ __('Tampilkan password') }}'">
                                <span x-show="!show" class="flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </span>
                                <span x-show="show" class="flex items-center" style="display: none;">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        @error('password') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 5. Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Konfirmasi Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password_confirmation" wire:model="password_confirmation" placeholder="{{ __('Ulangi password di atas') }}"
                                   class="w-full text-xs pl-9 pr-10 py-2.5 rounded-lg border @error('password_confirmation') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                            <button type="button" 
                                    @click="show = !show"
                                    tabindex="-1"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none cursor-pointer"
                                    :title="show ? '{{ __('Sembunyikan password') }}' : '{{ __('Tampilkan password') }}'">
                                <span x-show="!show" class="flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </span>
                                <span x-show="show" class="flex items-center" style="display: none;">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        @error('password_confirmation') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="register">{{ __('Kirim Pendaftaran Akun') }}</span>
                            <span wire:loading wire:target="register" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                {{ __('Memproses Pendaftaran...') }}
                            </span>
                        </button>
                    </div>

                </form>

                <!-- Link to Login -->
                <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-600 dark:text-slate-400">
                    {{ __('Sudah memiliki akun terdaftar?') }}
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 transition underline ml-1">
                        {{ __('Masuk ke Sistem') }}
                    </a>
                </div>

            </div>

        @else
            <!-- ================= SUCCESSFUL REGISTRATION: WAITING FOR ADMIN ACC ================= -->
            <div class="p-4 sm:p-6 md:p-8 text-center animate-fade-in">
                <!-- Icon Pulse Indicator -->
                <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-950/60 border-2 border-amber-300 dark:border-amber-700 flex items-center justify-center mx-auto mb-4 text-amber-600 dark:text-amber-400 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Pendaftaran Berhasil Dikirim!') }}</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                    {{ __('Terima kasih telah mendaftar. Data akun karyawan Anda telah berhasil kami simpan ke dalam sistem.') }}
                </p>

                <!-- Notice Status Card: Menunggu ACC Admin -->
                <div class="my-5 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-left shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-amber-500 text-white shadow-2xs">
                            {{ __('Menunggu ACC / Konfirmasi Admin') }}
                        </span>
                    </div>
                    <p class="text-xs text-amber-900 dark:text-amber-200/90 leading-relaxed">
                        {{ __('Sesuai kebijakan keamanan internal perusahaan, akun baru yang didaftarkan') }} <strong>{{ __('belum aktif') }}</strong> {{ __('dan') }} <strong>{{ __('belum bisa digunakan untuk login') }}</strong> {{ __('sampai pihak Administrator IT menyetujui (meng-ACC) akun Anda.') }}
                    </p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 mt-2 font-medium">
                        &bull; {{ __('Silakan tunggu atau hubungi Administrator IT untuk segera mengonfirmasi akun Anda.') }}
                    </p>
                </div>

                <!-- Registration Summary Details -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700 text-left text-xs space-y-2 mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Nama Lengkap:') }}</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $name }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Email Terdaftar:') }}</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $email }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('No. HP / WhatsApp:') }}</span>
                        <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $whatsapp_number }}</strong>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-200 dark:border-slate-700">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Status Akun:') }}</span>
                        <span class="inline-flex items-center gap-1 font-bold text-[10px] text-amber-700 dark:text-amber-300">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            {{ __('Belum Aktif (Menunggu ACC)') }}
                        </span>
                    </div>
                </div>

                <!-- Actions Button -->
                <div class="space-y-3">
                    <a href="{{ route('login') }}" 
                       class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>{{ __('Ke Halaman Login') }}</span>
                    </a>

                    <button type="button" 
                            wire:click="resetForm" 
                            class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium underline transition cursor-pointer">
                        {{ __('Daftarkan Akun Karyawan Lain') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- Card Footer -->
        <div class="bg-slate-50 dark:bg-slate-800/80 px-4 sm:px-6 py-2.5 sm:py-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
            <span>&copy; {{ date('Y') }} PT. Asia Plastik</span>
            <a href="{{ route('login') }}" class="hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">&larr; {{ __('Kembali ke Login') }}</a>
        </div>

    </div>
</div>
