<div class="relative" x-data="{ open: false }">
    @if ($currentUser)
        <!-- Header Button: Logged In User Avatar & Information -->
        <button @click="open = !open" type="button" 
            class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md flex items-center gap-3 cursor-pointer transition text-white">
            <div class="relative">
                <img src="{{ $currentUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=0284c7&color=fff' }}" 
                     alt="{{ $currentUser->name }}" 
                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUser->name) }}&background=0284c7&color=fff';"
                     class="w-8 h-8 rounded-full object-cover border border-white/40 shadow-xs" />
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border border-white rounded-full"></span>
            </div>
            <div class="text-left hidden sm:block">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-semibold text-white leading-tight">{{ $currentUser->name }}</p>
                    <span class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded bg-blue-500/40 text-blue-100 border border-blue-400/30">
                        {{ $currentUser->role ?? 'Staff' }}
                    </span>
                </div>
                <p class="text-[10px] text-blue-200 leading-tight">{{ $currentUser->department?->name ?? 'General Staff' }}</p>
            </div>
            <svg class="w-3.5 h-3.5 text-blue-200 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Profile Dropdown Menu for Logged In User -->
        <div x-show="open" @click.away="open = false" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 text-slate-800"
             style="display: none;">
            
            <!-- User Identity Header Banner -->
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 p-4 text-white">
                <div class="flex items-center gap-3">
                    <img src="{{ $currentUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=0284c7&color=fff' }}" 
                         alt="{{ $currentUser->name }}" 
                         onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUser->name) }}&background=0284c7&color=fff';"
                         class="w-12 h-12 rounded-full object-cover border-2 border-white/60 shadow-md shrink-0" />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h4 class="text-xs font-bold text-white truncate">{{ $currentUser->name }}</h4>
                        </div>
                        <p class="text-[11px] text-blue-200 truncate">{{ $currentUser->email }}</p>
                        <div class="mt-1 flex items-center gap-1.5">
                            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-blue-500/40 text-blue-100 border border-blue-400/30">
                                {{ $currentUser->role ?? 'Staff' }}
                            </span>
                            <span class="text-[9px] font-medium text-white/80">
                                &bull; {{ $currentUser->department?->name ?? 'Corporate' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Profil Karyawan -->
            <div class="p-3 space-y-2 text-xs bg-slate-50 border-b border-gray-100">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Status Akun:</span>
                    @if ($currentUser->email_verified_at)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Terverifikasi OTP
                        </span>
                    @else
                        <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                            Belum Verifikasi
                        </span>
                    @endif
                </div>

                @if ($currentUser->national_id_ktp)
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Nomor KTP (NIK):</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $currentUser->national_id_ktp }}</span>
                    </div>
                @endif

                @if ($currentUser->whatsapp_number)
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">WhatsApp:</span>
                        <span class="font-semibold text-slate-800">{{ $currentUser->whatsapp_number }}</span>
                    </div>
                @endif

                @if ($currentUser->complete_address)
                    <div class="pt-1 border-t border-slate-200 text-[11px]">
                        <span class="text-slate-500 block mb-0.5">Alamat:</span>
                        <p class="text-slate-700 leading-tight text-[10px] line-clamp-2">{{ $currentUser->complete_address }}</p>
                    </div>
                @endif
            </div>

            @if ($users->count() > 1)
                <!-- Perspective Switcher (Simulasi Role) -->
                <div class="p-3 border-b border-gray-100 bg-white">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Ganti Akun Demo / Perspektif:</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto">
                        @foreach ($users as $user)
                            <button wire:click="switchUser({{ $user->id }})" type="button"
                                class="w-full text-left p-1.5 rounded-lg flex items-center justify-between text-xs transition cursor-pointer {{ $user->id === $currentUser->id ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:bg-slate-50 text-slate-700' }}">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2 h-2 rounded-full {{ $user->id === $currentUser->id ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                                    <span class="truncate">{{ $user->name }}</span>
                                </div>
                                <span class="text-[9px] uppercase px-1 py-0.2 rounded bg-slate-100 text-slate-500">
                                    {{ $user->role }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tombol Logout -->
            <div class="p-2 bg-white">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 rounded-lg text-rose-600 hover:bg-rose-50 font-semibold transition text-xs flex items-center justify-between cursor-pointer">
                        <span>Keluar Akun (Logout)</span>
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>

    @else
        <!-- Header Button: Guest / Unauthenticated State -->
        <button @click="open = !open" type="button" 
            class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md flex items-center gap-2.5 cursor-pointer transition text-white group">
            <div class="w-8 h-8 rounded-full bg-slate-800/80 border border-white/30 flex items-center justify-center text-slate-300 group-hover:text-white transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="text-left hidden sm:block">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-semibold text-white leading-tight">Belum Masuk</p>
                    <span class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded bg-amber-500/30 text-amber-200 border border-amber-400/30">
                        TAMU
                    </span>
                </div>
                <p class="text-[10px] text-blue-200 leading-tight">Klik untuk Login / Daftar</p>
            </div>
            <svg class="w-3.5 h-3.5 text-blue-200 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Guest Dropdown Menu -->
        <div x-show="open" @click.away="open = false" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 text-slate-800"
             style="display: none;">
            
            <!-- Guest Header Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-4 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-slate-200 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Portal Layanan Tiket</h4>
                        <span class="inline-block mt-0.5 text-[9px] uppercase font-bold px-1.5 py-0.2 rounded bg-amber-500/30 text-amber-200 border border-amber-400/30">
                            Akses Tamu
                        </span>
                    </div>
                </div>
            </div>

            <!-- Guest Info Body -->
            <div class="p-4 text-xs text-slate-600 bg-slate-50 border-b border-gray-100 leading-relaxed">
                Anda saat ini belum masuk ke akun. Silakan login atau daftarkan akun baru untuk mengirim tiket dan mengakses komunikasi tim.
            </div>

            <!-- Login / Register Action Buttons -->
            <div class="p-3 bg-white space-y-2">
                <a href="{{ route('login') }}" 
                   class="w-full py-2.5 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Masuk ke Akun (Login)</span>
                </a>
                <a href="{{ route('register') }}" 
                   class="w-full py-2 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs flex items-center justify-center gap-2 border border-slate-300 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span>Registrasi Akun Baru (KTP)</span>
                </a>
            </div>
        </div>
    @endif
</div>
