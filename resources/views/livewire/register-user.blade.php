<div class="min-h-screen relative flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-slate-900 overflow-y-auto"
     style="background-image: url('/images/fotopt_2.webp'); background-size: cover; background-position: center;">
    
    <!-- Dark Blue Overlay -->
    <div class="absolute inset-0 bg-blue-900/80 backdrop-blur-[2px]"></div>

    <!-- Main Auth Card -->
    <div class="relative z-10 w-full max-w-4xl bg-white shadow-2xl border border-gray-100 rounded-xl overflow-hidden my-8">
        
        <!-- Corporate Top Bar -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 px-6 py-4 flex items-center justify-between text-white border-b border-blue-700/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/10 border border-white/20 flex items-center justify-center font-black text-sm tracking-wider shadow">
                    AP
                </div>
                <div>
                    <h2 class="font-extrabold text-sm sm:text-base tracking-wide text-white">PT. ASIA PLASTIK</h2>
                    <p class="text-[10px] text-blue-200 tracking-wider uppercase font-semibold">Corporate Ticketing & Communication System</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $step === 1 ? 'bg-blue-500/30 text-blue-200 border border-blue-400/30' : 'bg-emerald-500/30 text-emerald-200 border border-emerald-400/30' }}">
                    {{ $step === 1 ? 'Langkah 1: Formulir Data Diri' : 'Langkah 2: Verifikasi OTP' }}
                </span>
            </div>
        </div>

        @if ($step === 1)
            <!-- ================= STEP 1: REGISTRATION FORM ================= -->
            <div class="p-6 sm:p-8 lg:p-10">
                <div class="mb-6 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pendaftaran Akun Karyawan</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi data pribadi dan kredensial untuk mengakses sistem komunikasi & ticketing internal.</p>
                </div>

                @if ($errorMessage)
                    <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $errorMessage }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="register" class="space-y-6">
                    <!-- Profile Picture Upload Placeholder & Preview -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-300 flex flex-col sm:flex-row items-center gap-4">
                        <div class="relative">
                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="w-20 h-20 rounded-full object-cover border-2 border-blue-600 shadow-md">
                            @else
                                <div class="w-20 h-20 rounded-full bg-blue-100 border-2 border-dashed border-blue-300 flex flex-col items-center justify-center text-blue-600">
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            @endif
                            <div wire:loading wire:target="avatar" class="absolute inset-0 bg-white/80 rounded-full flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            </div>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Foto Profil Karyawan (Opsional)</label>
                            <p class="text-[11px] text-slate-500 mb-2">Format: JPG, JPEG, PNG, atau WEBP (Maksimum 10MB). Jika dikosongkan, avatar inisial akan digenerate otomatis.</p>
                            <input type="file" wire:model="avatar" accept="image/*" id="avatar_input" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            @error('avatar') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 2-COLUMN GRID (DESKTOP) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- LEFT COLUMN: Data Pribadi & Kontak -->
                        <div class="space-y-4">
                            <div class="border-b border-slate-200 pb-2 mb-2">
                                <h3 class="text-xs font-black uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    1. Informasi Pribadi &amp; Kontak
                                </h3>
                            </div>

                            <!-- 1. Full Name -->
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" id="name" wire:model="name" placeholder="Contoh: Budi Santoso"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('name') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                @error('name') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 2. Gender -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition text-xs {{ $gender === 'male' ? 'border-blue-600 bg-blue-50/60 font-bold text-blue-900' : 'border-gray-200 hover:bg-slate-50 text-slate-600' }}">
                                        <input type="radio" wire:model.live="gender" value="male" class="text-blue-600 focus:ring-blue-500">
                                        <span>Laki-laki (Male)</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition text-xs {{ $gender === 'female' ? 'border-blue-600 bg-blue-50/60 font-bold text-blue-900' : 'border-gray-200 hover:bg-slate-50 text-slate-600' }}">
                                        <input type="radio" wire:model.live="gender" value="female" class="text-blue-600 focus:ring-blue-500">
                                        <span>Perempuan (Female)</span>
                                    </label>
                                </div>
                                @error('gender') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 3. WhatsApp Number -->
                            <div>
                                <label for="whatsapp_number" class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="tel" id="whatsapp_number" wire:model="whatsapp_number" placeholder="Contoh: 081234567890"
                                           class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('whatsapp_number') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5">Kode OTP dan notifikasi tiket akan dikirimkan ke nomor ini.</p>
                                @error('whatsapp_number') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 4. Complete Address -->
                            <div>
                                <label for="complete_address" class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap Domisili <span class="text-rose-500">*</span></label>
                                <textarea id="complete_address" wire:model="complete_address" rows="3" placeholder="Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten"
                                          class="w-full text-xs px-3.5 py-2 rounded-lg border @error('complete_address') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition resize-none"></textarea>
                                @error('complete_address') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 5. Postal Code -->
                            <div>
                                <label for="postal_code" class="block text-xs font-bold text-slate-700 mb-1">Kode Pos <span class="text-rose-500">*</span></label>
                                <input type="text" id="postal_code" wire:model="postal_code" maxlength="10" placeholder="Contoh: 60293"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('postal_code') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                @error('postal_code') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Identitas Resmi, Departemen & Akun -->
                        <div class="space-y-4">
                            <div class="border-b border-slate-200 pb-2 mb-2">
                                <h3 class="text-xs font-black uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    2. Kredensial &amp; Akses Sistem
                                </h3>
                            </div>

                            <!-- 6. National ID (KTP) -->
                            <div>
                                <label for="national_id_ktp" class="block text-xs font-bold text-slate-700 mb-1">Nomor KTP (NIK 16 Digit) <span class="text-rose-500">*</span></label>
                                <input type="text" id="national_id_ktp" wire:model="national_id_ktp" maxlength="16" placeholder="Contoh: 3578012345670001"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('national_id_ktp') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition font-mono">
                                <p class="text-[10px] text-slate-400 mt-0.5">Dapat digunakan sebagai identitas login utama ke portal ticketing.</p>
                                @error('national_id_ktp') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 7. Department Dropdown -->
                            <div>
                                <label for="department_id" class="block text-xs font-bold text-slate-700 mb-1">Departemen / Divisi <span class="text-rose-500">*</span></label>
                                <select id="department_id" wire:model="department_id"
                                        class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('department_id') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition bg-white">
                                    <option value="">-- Pilih Departemen Penugasan --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 8. Email Address -->
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email Perusahaan / Pribadi <span class="text-rose-500">*</span></label>
                                <input type="email" id="email" wire:model="email" placeholder="nama@perusahaan.com"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('email') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                @error('email') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 9. Password -->
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password Akses <span class="text-rose-500">*</span></label>
                                <input type="password" id="password" wire:model="password" placeholder="Minimal 8 karakter"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('password') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                @error('password') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- 10. Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                                <input type="password" id="password_confirmation" wire:model="password_confirmation" placeholder="Ulangi password di atas"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('password_confirmation') border-rose-400 bg-rose-50 @else border-gray-300 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                @error('password_confirmation') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>

                    <!-- SUBMIT BUTTON & FOOTER ACTIONS -->
                    <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-600">
                            Sudah memiliki akun terdaftar? 
                            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 transition underline">
                                Masuk ke Sistem
                            </a>
                        </div>
                        
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="register">Kirim Pendaftaran &amp; Dapatkan OTP</span>
                            <span wire:loading wire:target="register" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                Memproses Data...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

        @else
            <!-- ================= STEP 2: OTP VERIFICATION UI ================= -->
            <div class="p-6 sm:p-10 lg:p-12 text-center max-w-xl mx-auto">
                <!-- Icon Pulse Indicator -->
                <div class="w-16 h-16 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center mx-auto mb-4 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>

                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Verifikasi Kode Keamanan (OTP)</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Kami telah mengirimkan 6 digit kode verifikasi untuk memvalidasi akun 
                    <strong class="text-slate-800 font-semibold">{{ $email }}</strong> (WhatsApp: <strong class="text-slate-800 font-semibold">{{ $whatsapp_number }}</strong>).
                </p>

                <!-- Demo Instant Testing Badge -->
                @if ($generatedOtpDemo)
                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs inline-flex items-center gap-2">
                        <span class="font-bold uppercase tracking-wider bg-amber-200 px-2 py-0.5 rounded text-[10px]">Testing Helper</span>
                        <span>Kode OTP Anda di database: <strong class="font-mono text-sm tracking-widest text-amber-900 font-black">{{ $generatedOtpDemo }}</strong></span>
                    </div>
                @endif

                @if ($successMessage)
                    <div class="mt-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
                        {{ $successMessage }}
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mt-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- 6 ROUNDED INPUT BOXES -->
                <div class="my-8" x-data="otpForm()">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Masukkan 6-Digit Kode OTP</label>
                    <div class="flex justify-center items-center gap-2 sm:gap-3">
                        <input type="text" maxlength="1" id="otp-1" wire:model.defer="otp1"
                               x-ref="otp1" @input="onInput(1, $event)" @keydown="onKeydown(1, $event)" @paste="onPaste($event)"
                               class="w-11 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-blue-900 bg-slate-50 transition outline-none" autocomplete="off" autofocus>
                        <input type="text" maxlength="1" id="otp-2" wire:model.defer="otp2"
                               x-ref="otp2" @input="onInput(2, $event)" @keydown="onKeydown(2, $event)" @paste="onPaste($event)"
                               class="w-11 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-blue-900 bg-slate-50 transition outline-none" autocomplete="off">
                        <input type="text" maxlength="1" id="otp-3" wire:model.defer="otp3"
                               x-ref="otp3" @input="onInput(3, $event)" @keydown="onKeydown(3, $event)" @paste="onPaste($event)"
                               class="w-11 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-blue-900 bg-slate-50 transition outline-none" autocomplete="off">
                        <input type="text" maxlength="1" id="otp-4" wire:model.defer="otp4"
                               x-ref="otp4" @input="onInput(4, $event)" @keydown="onKeydown(4, $event)" @paste="onPaste($event)"
                               class="w-11 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-blue-900 bg-slate-50 transition outline-none" autocomplete="off">
                        <input type="text" maxlength="1" id="otp-5" wire:model.defer="otp5"
                               x-ref="otp5" @input="onInput(5, $event)" @keydown="onKeydown(5, $event)" @paste="onPaste($event)"
                               class="w-11 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-blue-900 bg-slate-50 transition outline-none" autocomplete="off">
                        <input type="text" maxlength="1" id="otp-6" wire:model.defer="otp6"
                               x-ref="otp6" @input="onInput(6, $event)" @keydown="onKeydown(6, $event)" @paste="onPaste($event)"
                               class="w-11 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 text-blue-900 bg-slate-50 transition outline-none" autocomplete="off">
                    </div>
                </div>

                <!-- VERIFY BUTTON -->
                <div class="space-y-4">
                    <button type="button" 
                            wire:click="verifyOtp" 
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="verifyOtp">Verifikasi Akun &amp; Masuk</span>
                        <span wire:loading wire:target="verifyOtp" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Memverifikasi OTP...
                        </span>
                    </button>

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-200">
                        <button type="button" wire:click="$set('step', 1)" class="text-slate-600 hover:text-slate-800 font-medium underline cursor-pointer">
                            &larr; Ubah Data Registrasi
                        </button>
                        <button type="button" wire:click="resendOtp" class="text-blue-600 hover:text-blue-800 font-bold transition cursor-pointer">
                            Kirim Ulang Kode OTP
                        </button>
                    </div>
                </div>
            </div>

            <!-- Auto-advance OTP Alpine.js Component -->
            <script>
                function otpForm() {
                    return {
                        onInput(index, event) {
                            const val = event.target.value;
                            if (val.length === 1 && index < 6) {
                                const nextInput = this.$refs['otp' + (index + 1)];
                                if (nextInput) nextInput.focus();
                            }
                        },
                        onKeydown(index, event) {
                            if (event.key === 'Backspace' && !event.target.value && index > 1) {
                                const prevInput = this.$refs['otp' + (index - 1)];
                                if (prevInput) {
                                    prevInput.focus();
                                }
                            }
                        },
                        onPaste(event) {
                            event.preventDefault();
                            const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim();
                            if (/^\d{6}$/.test(pasteData)) {
                                for (let i = 1; i <= 6; i++) {
                                    const digit = pasteData[i - 1];
                                    this.$refs['otp' + i].value = digit;
                                    this.$wire.set('otp' + i, digit);
                                }
                                this.$refs.otp6.focus();
                            }
                        }
                    };
                }
            </script>
        @endif

        <!-- Card Footer -->
        <div class="bg-slate-50 px-6 py-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-slate-400">
            <span>&copy; {{ date('Y') }} PT. Asia Plastik &bull; Keamanan Akun Terenkripsi</span>
            <a href="{{ url('/') }}" class="hover:text-blue-600 font-medium transition">&larr; Kembali ke Beranda</a>
        </div>

    </div>
</div>
