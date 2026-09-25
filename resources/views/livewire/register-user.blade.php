<div class="min-h-screen relative flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-slate-900 overflow-y-auto"
     style="background-image: url('/images/fotopt_2.webp'); background-size: cover; background-position: center;">
    
    <!-- Dark Blue Overlay -->
    <div class="absolute inset-0 bg-blue-900/80 backdrop-blur-[2px]"></div>

    <!-- Main Auth Card -->
    <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 shadow-2xl border border-gray-100 dark:border-slate-800 rounded-xl overflow-hidden my-8 transition-colors">
        
        <!-- Corporate Top Bar -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 px-6 py-4 flex items-center justify-between text-white border-b border-blue-700/50">
            <a href="{{ url('/') }}" class="flex items-center group">
                <img src="{{ asset('images/logo2.webp') }}" alt="PT. Asia Plastik" class="h-10 sm:h-11 w-auto object-contain transition duration-200 group-hover:opacity-90">
            </a>
            <div class="text-right hidden sm:block">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $step === 1 ? 'bg-blue-500/30 text-blue-200 border border-blue-400/30' : 'bg-emerald-500/30 text-emerald-200 border border-emerald-400/30' }}">
                    {{ $step === 1 ? __('Langkah 1: Formulir Data Diri') : __('Langkah 2: Verifikasi OTP') }}
                </span>
            </div>
        </div>

        @if ($step === 1)
            <!-- ================= STEP 1: REGISTRATION FORM ================= -->
            <div class="p-6 sm:p-8">
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
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ __('Kode OTP verifikasi akan dikirimkan ke alamat email ini.') }}</p>
                        @error('email') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 4. Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input type="password" id="password" wire:model="password" placeholder="{{ __('Minimal 8 karakter') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('password') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        @error('password') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 5. Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Konfirmasi Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" placeholder="{{ __('Ulangi password di atas') }}"
                                   class="w-full text-xs pl-9 pr-3.5 py-2.5 rounded-lg border @error('password_confirmation') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        @error('password_confirmation') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="register">{{ __('Kirim Pendaftaran & Dapatkan OTP') }}</span>
                            <span wire:loading wire:target="register" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                {{ __('Memproses Data...') }}
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
            <!-- ================= STEP 2: OTP VERIFICATION UI ================= -->
            <div class="p-6 sm:p-8 text-center">
                <!-- Icon Pulse Indicator -->
                <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center mx-auto mb-4 text-blue-600 dark:text-blue-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Verifikasi Kode Keamanan (OTP)') }}</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1.5">
                    {{ __('Kode verifikasi 6 digit telah dikirimkan ke email:') }}<br>
                    <strong class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ $email }}</strong>
                </p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">
                    {{ __('Kontak Terdaftar:') }} <span class="font-medium text-slate-600 dark:text-slate-300 font-mono">{{ $whatsapp_number }}</span>
                </p>

                @if ($successMessage)
                    <div class="mt-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs">
                        {{ $successMessage }}
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mt-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- 6 ROUNDED INPUT BOXES WITH AUTO-ADVANCE & BACKSPACE NAVIGATION -->
                <div class="my-6" 
                     x-data="{
                         init() {
                             this.$nextTick(() => {
                                 this.$refs.otp1?.focus();
                             });
                         },
                         handleInput(index, event) {
                             const input = event.target;
                             let val = input.value.replace(/\D/g, '');
                             if (val.length > 1) {
                                 val = val.slice(-1);
                             }
                             input.value = val;
                             this.$wire.set('otp' + index, val, false);

                             if (val && index < 6) {
                                 this.$nextTick(() => {
                                     const next = this.$refs['otp' + (index + 1)];
                                     if (next) {
                                         next.focus();
                                         next.select();
                                     }
                                 });
                             }
                         },
                         handleKeydown(index, event) {
                             const input = event.target;
                             if (event.key === 'Backspace') {
                                 if (!input.value && index > 1) {
                                     event.preventDefault();
                                     const prev = this.$refs['otp' + (index - 1)];
                                     if (prev) {
                                         prev.focus();
                                         prev.value = '';
                                         this.$wire.set('otp' + (index - 1), '', false);
                                     }
                                 }
                             } else if (event.key === 'ArrowLeft' && index > 1) {
                                 event.preventDefault();
                                 this.$refs['otp' + (index - 1)]?.focus();
                             } else if (event.key === 'ArrowRight' && index < 6) {
                                 event.preventDefault();
                                 this.$refs['otp' + (index + 1)]?.focus();
                             } else if (event.key === 'Enter') {
                                 event.preventDefault();
                                 this.$wire.verifyOtp();
                             }
                         },
                         handlePaste(event) {
                             event.preventDefault();
                             const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim();
                             const digits = pasteData.replace(/\D/g, '');
                             if (digits.length > 0) {
                                 for (let i = 1; i <= 6; i++) {
                                     const digit = digits[i - 1] || '';
                                     const ref = this.$refs['otp' + i];
                                     if (ref) {
                                         ref.value = digit;
                                         this.$wire.set('otp' + i, digit, false);
                                     }
                                 }
                                 const focusIndex = Math.min(digits.length + 1, 6);
                                 this.$refs['otp' + focusIndex]?.focus();
                             }
                         }
                     }">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-3">{{ __('Masukkan 6-Digit Kode OTP') }}</label>
                    <div class="flex justify-center items-center gap-2 sm:gap-2.5">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" id="otp-1" wire:model="otp1"
                               x-ref="otp1" @focus="$event.target.select()" @input="handleInput(1, $event)" @keydown="handleKeydown(1, $event)" @paste="handlePaste($event)"
                               class="w-10 h-13 sm:w-12 sm:h-14 text-center text-xl font-black rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40 text-blue-900 dark:text-blue-100 bg-slate-50 dark:bg-slate-800 transition outline-none" autocomplete="one-time-code" autofocus>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" id="otp-2" wire:model="otp2"
                               x-ref="otp2" @focus="$event.target.select()" @input="handleInput(2, $event)" @keydown="handleKeydown(2, $event)" @paste="handlePaste($event)"
                               class="w-10 h-13 sm:w-12 sm:h-14 text-center text-xl font-black rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40 text-blue-900 dark:text-blue-100 bg-slate-50 dark:bg-slate-800 transition outline-none" autocomplete="one-time-code">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" id="otp-3" wire:model="otp3"
                               x-ref="otp3" @focus="$event.target.select()" @input="handleInput(3, $event)" @keydown="handleKeydown(3, $event)" @paste="handlePaste($event)"
                               class="w-10 h-13 sm:w-12 sm:h-14 text-center text-xl font-black rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40 text-blue-900 dark:text-blue-100 bg-slate-50 dark:bg-slate-800 transition outline-none" autocomplete="one-time-code">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" id="otp-4" wire:model="otp4"
                               x-ref="otp4" @focus="$event.target.select()" @input="handleInput(4, $event)" @keydown="handleKeydown(4, $event)" @paste="handlePaste($event)"
                               class="w-10 h-13 sm:w-12 sm:h-14 text-center text-xl font-black rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40 text-blue-900 dark:text-blue-100 bg-slate-50 dark:bg-slate-800 transition outline-none" autocomplete="one-time-code">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" id="otp-5" wire:model="otp5"
                               x-ref="otp5" @focus="$event.target.select()" @input="handleInput(5, $event)" @keydown="handleKeydown(5, $event)" @paste="handlePaste($event)"
                               class="w-10 h-13 sm:w-12 sm:h-14 text-center text-xl font-black rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40 text-blue-900 dark:text-blue-100 bg-slate-50 dark:bg-slate-800 transition outline-none" autocomplete="one-time-code">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" id="otp-6" wire:model="otp6"
                               x-ref="otp6" @focus="$event.target.select()" @input="handleInput(6, $event)" @keydown="handleKeydown(6, $event)" @paste="handlePaste($event)"
                               class="w-10 h-13 sm:w-12 sm:h-14 text-center text-xl font-black rounded-lg border-2 border-slate-200 dark:border-slate-700 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40 text-blue-900 dark:text-blue-100 bg-slate-50 dark:bg-slate-800 transition outline-none" autocomplete="one-time-code">
                    </div>
                </div>

                <!-- VERIFY BUTTON -->
                <div class="space-y-4">
                    <button type="button" 
                            wire:click="verifyOtp" 
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="verifyOtp">{{ __('Verifikasi Akun & Selesai') }}</span>
                        <span wire:loading wire:target="verifyOtp" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            {{ __('Memverifikasi OTP...') }}
                        </span>
                    </button>

                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" wire:click="$set('step', 1)" class="text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium underline cursor-pointer">
                            &larr; {{ __('Ubah Data') }}
                        </button>
                        <button type="button" wire:click="resendOtp" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-bold transition cursor-pointer">
                            {{ __('Kirim Ulang Kode OTP') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Footer -->
        <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
            <span>&copy; {{ date('Y') }} PT. Asia Plastik</span>
            <a href="{{ route('login') }}" class="hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">&larr; {{ __('Kembali ke Login') }}</a>
        </div>

    </div>
</div>
