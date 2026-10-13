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
                    <span class="hidden lg:inline text-slate-500">•</span>
                    <span class="hidden lg:inline text-slate-400 text-[10px] uppercase tracking-wider truncate">{{ __('Portal Layanan Tiket & Komunikasi Internal') }}</span>
                </div>

                <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0 ml-2">
                    @auth
                        @if (Auth::user()->email === 'user123@gmail.com')
                            <a href="{{ request()->is('/') ? '#hcm-master-section' : url('/#hcm-master-section') }}" 
                               class="hidden lg:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-[10px] uppercase tracking-wider shadow-xs transition">
                                <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>{{ __('Master Karyawan') }}</span>
                            </a>
                            <span class="hidden lg:inline text-slate-700">|</span>
                        @endif
                        <span class="text-emerald-300 font-semibold truncate max-w-[100px] text-[11px] sm:text-xs hidden md:inline">{{ Auth::user()->name }}</span>
                        <span class="text-slate-700 hidden md:inline">|</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-rose-300 text-rose-400 transition cursor-pointer font-bold text-[10px] sm:text-xs">{{ __('Logout') }}</button>
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
            <div class="h-14 sm:h-16 flex items-center justify-between gap-2 sm:gap-4">
                <!-- Left: ASIA PLASTIK Brand Logo -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center group shrink-0">
                        <img src="{{ asset('images/logo2.webp') }}" alt="Asia Plastik" class="h-7 sm:h-8 md:h-10 w-auto object-contain transition duration-200 group-hover:opacity-90 shrink-0">
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

        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- SLIDE-OVER MOBILE DRAWER NAVIGATION (Matches Reference Image 1)           -->
    <!-- ========================================================================= -->
    <div x-show="mobileMenuOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-hidden md:hidden"
         style="display: none;">
        
        <!-- Backdrop Dimmer Overlay -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-out duration-250"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false" 
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

        <!-- Drawer Content Panel (Full-width on phone, max-w-sm on tablet) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-full sm:max-w-md bg-white dark:bg-slate-900 shadow-2xl flex flex-col justify-between overflow-y-auto p-5 sm:p-6 transition-colors duration-200 z-10 text-slate-800 dark:text-slate-100">

            <div>
                <!-- 1. Drawer Header (Logo, Dark Mode Toggle Pill, Close Button) -->
                <div class="flex items-center justify-between pb-5 border-b border-gray-100 dark:border-slate-800 mb-5">
                    <!-- Brand Logo -->
                    <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="flex items-center">
                        <img src="{{ asset('images/logo2.webp') }}" alt="Asia Plastik" class="h-8 w-auto object-contain">
                    </a>

                    <!-- Header Right: Dark Mode Toggle Pill + Close Button -->
                    <div class="flex items-center gap-2.5">
                        <!-- Interactive Dark Mode Capsule Toggle (Matches Image 1) -->
                        <button type="button" 
                                @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }" 
                                class="flex items-center gap-1.5 px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer shadow-2xs select-none"
                                :title="darkMode ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
                            <!-- Moon icon -->
                            <span class="w-5 h-5 flex items-center justify-center transition-colors"
                                  :class="darkMode ? 'text-blue-400' : 'text-slate-400'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </span>
                            <!-- Sun icon with amber circular badge (Matches Image 1) -->
                            <span class="w-6 h-6 rounded-full flex items-center justify-center transition-all"
                                  :class="!darkMode ? 'bg-amber-400 text-slate-900 shadow-xs' : 'bg-slate-700 text-slate-300'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </span>
                        </button>

                        <!-- Close Button (✕) -->
                        <button type="button" 
                                @click="mobileMenuOpen = false" 
                                class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                title="{{ __('Tutup Menu') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 2. Language Selector Capsule Strip (Matches Image 1: 🌐 Lang: [ ID ] [ EN ] [ CN ]) -->
                <div class="bg-slate-100 dark:bg-slate-800/80 p-1.5 rounded-2xl flex items-center justify-between text-xs font-semibold mb-6">
                    <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 pl-2 font-medium">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        <span>Lang:</span>
                    </div>
                    <div class="flex items-center gap-1">
                        @foreach(['id' => 'ID', 'en' => 'EN', 'zh' => 'CN'] as $code => $label)
                            <a href="{{ route('locale.switch', $code) }}" 
                               class="px-4 py-1.5 rounded-xl transition text-center font-bold text-xs {{ $currentLocale === $code ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Navigation Menu Items (Vertical Cards with Left Icon Boxes - Matches Image 1) -->
                <nav class="space-y-2.5">
                    <!-- Beranda / Dashboard -->
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" 
                       class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition {{ request()->routeIs('dashboard') && !request()->has('ticket') ? 'bg-blue-50/80 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('dashboard') && !request()->has('ticket') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <span class="font-bold text-sm sm:text-base block {{ request()->routeIs('dashboard') && !request()->has('ticket') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-800 dark:text-slate-100' }}">
                                {{ __('Beranda') }}
                            </span>
                        </div>
                    </a>

                    @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->email === 'user123@gmail.com'))
                        <!-- ADMIN MENU ITEMS -->
                        @if (auth()->user()->email === 'user123@gmail.com')
                            <!-- Master Karyawan (HCM) -->
                            <a href="{{ request()->is('/') ? '#hcm-master-section' : url('/#hcm-master-section') }}" @click="mobileMenuOpen = false" 
                               class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="flex-1 text-left">
                                    <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                        {{ __('Master Karyawan (HCM)') }}
                                    </span>
                                </div>
                            </a>
                        @endif

                        <!-- Tangani Tiket Masuk -->
                        <a href="{{ request()->is('/') ? '#global-tickets' : url('/#global-tickets') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="flex-1 text-left flex items-center justify-between">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Tangani Tiket Masuk') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-600 text-white font-bold">
                                    {{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}
                                </span>
                            </div>
                        </a>

                        <!-- Riwayat Tiket Selesai -->
                        <a href="{{ request()->is('/') ? '#admin-resolved-history' : url('/#admin-resolved-history') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div class="flex-1 text-left flex items-center justify-between">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Riwayat Tiket Selesai') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-600 text-white font-bold">
                                    {{ \App\Models\Ticket::where('status', 'Resolved')->count() }}
                                </span>
                            </div>
                        </a>

                        <!-- Grafik Tren Harian -->
                        <a href="{{ request()->is('/') ? '#daily-ticket-chart-section' : url('/#daily-ticket-chart-section') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Grafik Tren Permintaan') }}
                                </span>
                            </div>
                        </a>

                    @elseif (auth()->check())
                        <!-- EMPLOYEE / STAFF MENU ITEMS -->
                        <!-- Buat Tiket Baru -->
                        <a href="{{ request()->is('/') ? '#new-ticket' : url('/#new-ticket') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Buat Tiket Baru') }}
                                </span>
                            </div>
                        </a>

                        <!-- Antrean Tiket Aktif -->
                        <a href="{{ request()->is('/') ? '#queue' : url('/#queue') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="flex-1 text-left flex items-center justify-between">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Antrean Tiket & Status') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-700 text-slate-200 font-bold">
                                    {{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}
                                </span>
                            </div>
                        </a>

                        <!-- Riwayat Tiket Selesai -->
                        <a href="{{ request()->is('/') ? '#resolved-history' : url('/#resolved-history') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div class="flex-1 text-left flex items-center justify-between">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Riwayat Tiket Selesai') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-600 text-white font-bold">
                                    {{ \App\Models\Ticket::where('status', 'Resolved')->count() }}
                                </span>
                            </div>
                        </a>

                    @else
                        <!-- GUEST MENU ITEMS -->
                        <!-- Antrean Tiket Publik -->
                        <a href="{{ request()->is('/') ? '#queue' : url('/#queue') }}" @click="mobileMenuOpen = false" 
                           class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                    {{ __('Antrean Tiket Publik') }}
                                </span>
                            </div>
                        </a>

                        <!-- Masuk Akun -->
                        <a href="{{ route('login') }}" class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <span class="font-bold text-sm sm:text-base text-blue-600 dark:text-blue-400 block">
                                    {{ __('Masuk ke Akun (Login)') }}
                                </span>
                            </div>
                        </a>

                        <!-- Daftar Baru -->
                        <a href="{{ route('register') }}" class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <span class="font-bold text-sm sm:text-base text-emerald-600 dark:text-emerald-400 block">
                                    {{ __('Daftar Akun Baru (Register)') }}
                                </span>
                            </div>
                        </a>
                    @endif

                    <!-- Hubungi Bantuan & WhatsApp (Matches Image 1 "Hubungi Penjualan") -->
                    <a href="https://wa.me/62318433078?text=Halo%20Admin%20PT.%20Asia%20Plastik%2C%20saya%20butuh%20bantuan%20terkait%20layanan%20sistem..." target="_blank" rel="noopener noreferrer" 
                       class="w-full flex items-center gap-3.5 p-2.5 rounded-2xl transition hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-[#25D366] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <span class="font-bold text-sm sm:text-base text-slate-800 dark:text-slate-100 block">
                                {{ __('Bantuan Operasional & WA') }}
                            </span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- 4. Bottom Representative Contact Card (Matches Reference Image 1) -->
            <div class="pt-6">
                <div class="rounded-3xl bg-slate-50 dark:bg-slate-800/60 p-4 border border-gray-100 dark:border-slate-800/80 space-y-3">
                    <p class="text-[10px] font-black tracking-wider uppercase text-slate-400 dark:text-slate-500">
                        {{ __('KONTAK REPRESENTATIF') }}
                    </p>

                    <!-- Email -->
                    <a href="mailto:marketing@asiaplastik.com" class="flex items-center gap-3 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <div class="w-8 h-8 rounded-xl bg-blue-100/80 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="truncate">marketing@asiaplastik.com</span>
                    </a>

                    <!-- Telepon -->
                    <a href="tel:+62318433078" class="flex items-center gap-3 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <div class="w-8 h-8 rounded-xl bg-blue-100/80 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <span>+6231 8433078</span>
                    </a>
                </div>

                @auth
                    <!-- Logout Action Button -->
                    <form action="{{ route('logout') }}" method="POST" class="pt-3">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold text-xs border border-rose-200 dark:border-rose-900/50 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>{{ __('Logout / Keluar dari Akun') }}</span>
                        </button>
                    </form>
                @endauth
            </div>

        </div>
    </div>

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
    <footer class="mt-auto border-t border-gray-200 dark:border-slate-800/80 bg-white dark:bg-slate-900 py-6 sm:py-8 text-xs text-slate-500 dark:text-slate-400 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4 text-center md:text-left">
            <div>
                <span class="font-bold text-slate-800 dark:text-slate-100">PT. ASIA PLASTIK</span> &bull; {{ __('Sistem Manajemen Tiket & Komunikasi Internal Manufaktur') }}
            </div>
            <div class="flex flex-wrap items-center justify-center md:justify-end gap-x-3.5 sm:gap-x-5 gap-y-1 text-[11px] text-slate-400 dark:text-slate-500">
                <span>{{ __('Kantor & Pabrik: Rungkut Industri, Surabaya') }}</span>
                <span>{{ __('Telp: +6231 8433078') }}</span>
                <span>&copy; {{ date('Y') }} {{ __('All Rights Reserved') }}</span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
