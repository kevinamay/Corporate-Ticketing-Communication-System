<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen bg-slate-100 dark:bg-slate-950 scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Asia Plastik - Corporate Ticketing & Communication System') }}</title>

    <!-- Theme Initialization (Prevent FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
@php
    if (!Auth::check() && session('active_user_id')) {
        $recoveredUser = \App\Models\User::find(session('active_user_id'));
        if ($recoveredUser) {
            Auth::login($recoveredUser);
        }
    }
    $currentLocale = app()->getLocale();
    $languages = [
        'id' => ['name' => 'Bahasa Indonesia', 'short' => 'ID', 'flag' => '🇮🇩'],
        'en' => ['name' => 'English', 'short' => 'EN', 'flag' => '🇬🇧'],
        'zh' => ['name' => '简体中文', 'short' => 'ZH', 'flag' => '🇨🇳'],
    ];
    $currentLang = $languages[$currentLocale] ?? $languages['id'];
@endphp
<body class="min-h-screen font-sans antialiased text-slate-800 dark:text-slate-100 bg-[#f4f6fa] dark:bg-[#0b1120] flex flex-col transition-colors duration-200" x-data="{ mobileMenuOpen: false, darkMode: document.documentElement.classList.contains('dark') }">

    <!-- MODERN CORPORATE INTERNAL NAVBAR (Clean, Executive & Aesthetic) -->
    <header class="sticky top-0 z-40 w-full bg-slate-900/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-800 shadow-md transition-colors">
        <div class="max-w-7xl 2xl:max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-10">
            <!-- Top Utility Bar (Active user, Quick Role info, Dark Mode, Language, Logout) -->
            <div class="py-1 sm:py-1.5 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-300 border-b border-slate-800/80 font-medium tracking-wide">
                <div class="flex items-center gap-1.5 sm:gap-2 text-slate-400 min-w-0">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                    <span class="font-bold text-slate-200 truncate">PT. ASIA PLASTIK</span>
                    <span class="hidden sm:inline text-slate-500">•</span>
                    <span class="hidden sm:inline text-slate-400 text-[10px] uppercase tracking-wider">{{ __('Portal Layanan Tiket & Komunikasi Internal') }}</span>
                </div>

                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    @auth
                        @if (Auth::user()->email === 'user123@gmail.com')
                            <a href="{{ request()->is('/') ? '#hcm-master-section' : url('/#hcm-master-section') }}" 
                               class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-[10px] uppercase tracking-wider shadow-xs transition">
                                <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>{{ __('Master Karyawan') }}</span>
                            </a>
                            <span class="hidden sm:inline text-slate-700">|</span>
                        @endif
                        <span class="text-emerald-300 font-semibold truncate max-w-[80px] sm:max-w-none text-[11px] sm:text-xs hidden xs:inline">{{ Auth::user()->name }}</span>
                        <span class="text-slate-700 hidden xs:inline">|</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-rose-300 text-rose-400 transition cursor-pointer font-bold text-[11px] sm:text-xs">{{ __('Logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white font-bold text-blue-300 transition text-[11px] sm:text-xs">{{ __('Masuk') }}</a>
                        <span class="text-slate-700">|</span>
                        <a href="{{ route('register') }}" class="hover:text-white font-bold text-slate-200 transition text-[11px] sm:text-xs">{{ __('Daftar') }}</a>
                    @endauth

                    <span class="text-slate-700">|</span>

                    <!-- Dark Mode Interactive Toggle Switch -->
                    <button type="button" @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }" 
                        class="flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full bg-slate-800 hover:bg-slate-700 border border-slate-700 transition cursor-pointer text-white font-bold text-[10px] shadow-xs"
                        :title="darkMode ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
                        <span x-show="darkMode" class="flex items-center gap-1 text-amber-300">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </span>
                        <span x-show="!darkMode" class="flex items-center gap-1 text-blue-300">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </span>
                    </button>

                    <span class="text-slate-700">|</span>

                    <!-- Multi-Language Dropdown Selector -->
                    <div class="relative" x-data="{ langOpen: false }" @click.away="langOpen = false">
                        <button type="button" @click="langOpen = !langOpen" 
                            class="flex items-center gap-1 px-1 sm:px-1.5 py-0.5 rounded hover:bg-slate-800 text-white font-semibold transition cursor-pointer select-none text-[11px] sm:text-xs"
                            title="{{ __('Pilih Bahasa') }}">
                            <span class="text-xs leading-none">{{ $currentLang['flag'] }}</span>
                            <span class="text-[10px] sm:text-[11px] font-bold">{{ $currentLang['short'] }}</span>
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': langOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="langOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute right-0 mt-2 w-44 rounded-xl bg-slate-900 border border-slate-700 shadow-2xl py-1 z-50 overflow-hidden" 
                             style="display: none;">
                            <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                                {{ __('Pilih Bahasa') }}
                            </div>
                            @foreach($languages as $code => $lang)
                                <a href="{{ route('locale.switch', $code) }}" 
                                   class="flex items-center justify-between px-3 py-2 text-xs font-semibold transition hover:bg-slate-800 {{ $currentLocale === $code ? 'text-blue-400 bg-slate-800/80 font-bold' : 'text-slate-200' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="text-sm leading-none">{{ $lang['flag'] }}</span>
                                        <span>{{ $lang['name'] }}</span>
                                    </span>
                                    @if($currentLocale === $code)
                                        <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Navigation Bar -->
            <div class="h-14 sm:h-16 flex items-center justify-between">
                <!-- Left: ASIA PLASTIK Brand Logo -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <a href="{{ url('/') }}" class="flex items-center group">
                        <img src="{{ asset('images/logo2.webp') }}" alt="Asia Plastik" class="h-8 sm:h-10 w-auto object-contain transition duration-200 group-hover:opacity-90">
                    </a>
                </div>

                <!-- Center: Navigation Links for Desktop & Laptop -->
                <nav class="hidden md:flex items-center gap-1.5 text-xs font-semibold">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-bold' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                    @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->email === 'user123@gmail.com'))
                        @if (auth()->user()->email === 'user123@gmail.com')
                            <a href="{{ request()->is('/') ? '#hcm-master-section' : url('/#hcm-master-section') }}" class="px-3 py-1.5 rounded-lg text-blue-300 hover:text-white hover:bg-blue-600 transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>{{ __('Master Karyawan (HCM)') }}</span>
                            </a>
                        @endif
                        <a href="{{ request()->is('/') ? '#global-tickets' : url('/#global-tickets') }}" class="px-3 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>{{ __('Tangani Tiket Masuk') }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-600 text-white font-bold">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</span>
                        </a>
                        <a href="{{ request()->is('/') ? '#admin-resolved-history' : url('/#admin-resolved-history') }}" class="px-3 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Riwayat Tiket Selesai') }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-600 text-white font-bold">{{ \App\Models\Ticket::where('status', 'Resolved')->count() }}</span>
                        </a>
                    @else
                        <a href="{{ request()->is('/') ? '#new-ticket' : url('/#new-ticket') }}" class="px-3 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition">
                            {{ __('Buat Tiket') }}
                        </a>
                        <a href="{{ request()->is('/') ? '#queue' : url('/#queue') }}" class="px-3 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                            <span>{{ __('Antrean Tiket') }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-700 text-slate-200 font-bold">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</span>
                        </a>
                        <a href="{{ request()->is('/') ? '#resolved-history' : url('/#resolved-history') }}" class="px-3 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Riwayat Tiket') }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-600 text-white font-bold">{{ \App\Models\Ticket::where('status', 'Resolved')->count() }}</span>
                        </a>
                    @endif
                </nav>

                <!-- Right: Search, Notifications, User Roster, Menu -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    <!-- Search Icon Button -->
                    <a href="{{ request()->is('/') ? '#queue' : url('/#queue') }}" title="{{ __('Cari Tiket') }}" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </a>

                    <!-- User Identity Switcher Component -->
                    <livewire:user-switcher />

                    <!-- Hamburger MENU button (Mobile / Tablet Portrait) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="md:hidden flex items-center gap-1 px-2 sm:px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer shrink-0">
                        <span>MENU</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Slide-down Menu for Mobile / Tablet -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden bg-slate-900 rounded-xl p-3 border border-slate-800 mb-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs shadow-xl" 
                 style="display: none;">
                <a href="{{ route('dashboard') }}" class="p-2.5 rounded-lg bg-slate-800 text-white font-bold text-center hover:bg-blue-600 transition">
                    {{ __('Dashboard') }}
                </a>
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->email === 'user123@gmail.com'))
                    @if (auth()->user()->email === 'user123@gmail.com')
                        <a href="{{ request()->is('/') ? '#hcm-master-section' : url('/#hcm-master-section') }}" @click="mobileMenuOpen = false" class="col-span-1 sm:col-span-2 p-2.5 rounded-lg bg-blue-600 text-white font-bold text-center hover:bg-blue-500 transition shadow-sm">
                            {{ __('Master Karyawan (HCM)') }}
                        </a>
                    @endif
                    <a href="{{ request()->is('/') ? '#global-tickets' : url('/#global-tickets') }}" @click="mobileMenuOpen = false" class="col-span-1 sm:col-span-2 p-2.5 rounded-lg bg-slate-800 text-white font-bold text-center hover:bg-blue-600 transition">
                        {{ __('Tangani Tiket Masuk') }}
                    </a>
                    <a href="{{ request()->is('/') ? '#admin-resolved-history' : url('/#admin-resolved-history') }}" @click="mobileMenuOpen = false" class="col-span-1 sm:col-span-2 p-2.5 rounded-lg bg-slate-800 text-emerald-300 font-bold text-center hover:bg-emerald-600 hover:text-white transition">
                        {{ __('Riwayat Tiket Selesai') }} ({{ \App\Models\Ticket::where('status', 'Resolved')->count() }})
                    </a>
                @else
                    <a href="{{ request()->is('/') ? '#new-ticket' : url('/#new-ticket') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-slate-800 text-blue-200 font-bold text-center hover:bg-blue-600 hover:text-white transition">
                        {{ __('Buat Tiket') }}
                    </a>
                    <a href="{{ request()->is('/') ? '#queue' : url('/#queue') }}" @click="mobileMenuOpen = false" class="p-2.5 rounded-lg bg-slate-800 text-blue-200 font-bold text-center hover:bg-blue-600 hover:text-white transition">
                        {{ __('Antrean Tiket') }}
                    </a>
                    <a href="{{ request()->is('/') ? '#resolved-history' : url('/#resolved-history') }}" @click="mobileMenuOpen = false" class="col-span-1 sm:col-span-2 p-2.5 rounded-lg bg-slate-800 text-emerald-300 font-bold text-center hover:bg-emerald-600 hover:text-white transition">
                        {{ __('Riwayat Tiket Selesai') }} ({{ \App\Models\Ticket::where('status', 'Resolved')->count() }})
                    </a>
                @endif

                <!-- Mobile Language Selector Row -->
                <div class="col-span-1 sm:col-span-2 p-2 rounded-lg bg-slate-800/60 border border-slate-700/60 mt-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 text-center">{{ __('Pilih Bahasa') }}</p>
                    <div class="grid grid-cols-3 gap-1.5">
                        @foreach($languages as $code => $lang)
                            <a href="{{ route('locale.switch', $code) }}" 
                                class="py-1.5 px-2 rounded text-center flex items-center justify-center gap-1 text-[11px] font-semibold transition {{ $currentLocale === $code ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-slate-700 text-slate-200 hover:bg-slate-600' }}">
                                <span>{{ $lang['flag'] }}</span>
                                <span>{{ $lang['short'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN BODY CONTENT AREA (Split-Screen & Desk Dashboard with clean spacing, no negative margins) -->
    <div class="flex-1 max-w-7xl 2xl:max-w-[1440px] w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 py-5 sm:py-6 lg:py-8 relative z-20">
        <main class="w-full">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Interactive AI Chatbot Assistant Widget ("Butuh Bantuan?" Floating Trigger & Modal) -->
    <livewire:ai-assistant />

    <!-- Corporate Footer -->
    <footer class="mt-auto border-t border-gray-200 dark:border-slate-800/80 bg-white dark:bg-slate-900 py-8 text-xs text-slate-500 dark:text-slate-400 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">AP</div>
                <div>
                    <span class="font-bold text-slate-800 dark:text-slate-100">PT. ASIA PLASTIK</span> &bull; {{ __('PT. ASIA PLASTIK • Sistem Manajemen Tiket & Komunikasi Internal Manufaktur') }}
                </div>
            </div>
            <div class="flex items-center gap-5 text-[11px] text-slate-400 dark:text-slate-500">
                <span>{{ __('Kantor & Pabrik: Rungkut Industri, Surabaya') }}</span>
                <span>{{ __('Telp: +6231 8433078') }}</span>
                <span>&copy; {{ date('Y') }} {{ __('All Rights Reserved') }}</span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
