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
    $currentLocale = app()->getLocale();
    $languages = [
        'id' => ['name' => 'Bahasa Indonesia', 'short' => 'ID', 'flag' => '🇮🇩'],
        'en' => ['name' => 'English', 'short' => 'EN', 'flag' => '🇬🇧'],
        'ja' => ['name' => '日本語', 'short' => 'JA', 'flag' => '🇯🇵'],
        'zh' => ['name' => '简体中文', 'short' => 'ZH', 'flag' => '🇨🇳'],
    ];
    $currentLang = $languages[$currentLocale] ?? $languages['id'];
@endphp
<body class="min-h-screen font-sans antialiased text-slate-800 dark:text-slate-100 bg-[#f4f6fa] dark:bg-[#0b1120] flex flex-col transition-colors duration-200" x-data="{ mobileMenuOpen: false, darkMode: document.documentElement.classList.contains('dark') }">

    <!-- TOP HERO SECTION (Asia Plastik industrial manufacturing entrance) -->
    <header class="relative w-full bg-slate-900 shadow-xl overflow-hidden" 
            style="background-image: url('/images/fotopt.webp'); background-size: cover; background-position: center;">
        
        <!-- Dark gradient overlay for high contrast and text readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-slate-950/90 pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Utility Bar (Email, Phone, Language) -->
            <div class="py-2.5 flex items-center justify-end text-[11px] text-white/80 border-b border-white/10 gap-4 sm:gap-5 font-medium tracking-wide">
                <a href="mailto:marketing@asiaplastik.com" class="hover:text-white transition flex items-center gap-1.5 hidden md:flex">
                    <svg class="w-3.5 h-3.5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>marketing@asiaplastik.com</span>
                </a>
                <span class="text-white/40 hidden md:inline">|</span>
                <a href="tel:+62318433078" class="hover:text-white transition flex items-center gap-1.5 hidden sm:flex">
                    <svg class="w-3.5 h-3.5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>+6231 8433078</span>
                </a>
                <span class="text-white/40 hidden sm:inline">|</span>
                @auth
                    @if ((int) Auth::user()->department_id === 2 || (int) Auth::user()->department_id === 4 || str_contains(strtolower(Auth::user()->department?->name ?? ''), 'hr'))
                        <a href="{{ url('/hcm-core/employees-master') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-[11px] uppercase tracking-wider shadow-sm transition">
                            <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>{{ __('Akses Vault HRD') }}</span>
                        </a>
                        <span class="text-white/40">|</span>
                    @endif
                    <span class="text-emerald-300 font-semibold truncate max-w-[140px] sm:max-w-none">{{ __('Aktif: ') }}{{ Auth::user()->name }}</span>
                    <span class="text-white/40">|</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-rose-300 text-rose-200 transition cursor-pointer font-bold">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white font-bold text-blue-200 transition">{{ __('Masuk (Login)') }}</a>
                    <span class="text-white/40">|</span>
                    <a href="{{ route('register') }}" class="hover:text-white font-bold text-white transition">{{ __('Registrasi KTP') }}</a>
                @endauth
                <span class="text-white/40">|</span>

                <!-- Dark Mode Interactive Toggle Switch -->
                <button type="button" @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }" 
                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 hover:bg-white/25 border border-white/20 transition cursor-pointer text-white font-bold text-[10px] sm:text-[11px] shadow-xs"
                    :title="darkMode ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
                    <span x-show="darkMode" class="flex items-center gap-1.5 text-amber-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>{{ __('Mode Terang') }}</span>
                    </span>
                    <span x-show="!darkMode" class="flex items-center gap-1.5 text-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <span>{{ __('Mode Gelap') }}</span>
                    </span>
                </button>

                <span class="text-white/40">|</span>

                <!-- Multi-Language Dropdown Selector -->
                <div class="relative" x-data="{ langOpen: false }" @click.away="langOpen = false">
                    <button type="button" @click="langOpen = !langOpen" 
                        class="flex items-center gap-1.5 px-2 py-0.5 rounded hover:bg-white/10 text-white font-bold transition cursor-pointer select-none"
                        title="{{ __('Pilih Bahasa') }}">
                        <span class="text-xs leading-none">{{ $currentLang['flag'] }}</span>
                        <span>{{ $currentLang['short'] }}</span>
                        <svg class="w-3 h-3 text-white/70 transition-transform duration-200" :class="{ 'rotate-180': langOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                         class="absolute right-0 mt-2 w-48 rounded-xl bg-slate-900/95 dark:bg-slate-900/95 backdrop-blur-md border border-white/20 shadow-2xl py-1 z-50 overflow-hidden" 
                         style="display: none;">
                        <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-white/10">
                            {{ __('Pilih Bahasa') }}
                        </div>
                        @foreach($languages as $code => $lang)
                            <a href="{{ route('locale.switch', $code) }}" 
                               class="flex items-center justify-between px-3 py-2 text-xs font-semibold transition hover:bg-white/15 {{ $currentLocale === $code ? 'text-blue-400 bg-white/10 font-bold' : 'text-slate-200' }}">
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

            <!-- Main Navigation Bar (Logo on Left, Tools & Menu on Right) -->
            <div class="h-24 flex items-center justify-between">
                <!-- Left: ASIA PLASTIK Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center group">
                    <img src="{{ asset('images/logo2.webp') }}" alt="Asia Plastik - Plastic Industry" class="h-11 sm:h-12 w-auto object-contain transition duration-200 group-hover:opacity-90">
                </a>

                <!-- Right: Search, Notifications, User Roster, Menu -->
                <div class="flex items-center gap-4 sm:gap-6">
                    <!-- Search Icon Button -->
                    <a href="#queue" title="Cari Tiket" class="text-white/80 hover:text-white p-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </a>

                    <!-- Notification Bell with Red Dot Alert -->
                    <div class="relative" title="Notifikasi Sistem">
                        <button class="p-2 text-white/80 hover:text-white transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </button>
                        <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                    </div>

                    <!-- User Identity Switcher Component -->
                    <livewire:user-switcher />

                    <!-- Hamburger MENU button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 text-white font-black text-xs tracking-wider uppercase transition cursor-pointer">
                        <span>MENU</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Slide-down Menu for Navigation Links -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 border border-white/15 mb-4 grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs shadow-2xl" 
                 style="display: none;">
                <a href="{{ url('/') }}" class="p-3 rounded-xl bg-white/10 text-white font-bold text-center hover:bg-blue-600 transition">
                    {{ __('Dashboard Utama') }}
                </a>
                <a href="#new-ticket" @click="mobileMenuOpen = false" class="p-3 rounded-xl bg-white/5 text-blue-200 font-bold text-center hover:bg-blue-600 hover:text-white transition">
                    {{ __('Form Buat Tiket') }}
                </a>
                <a href="#chat-pane" @click="mobileMenuOpen = false" class="p-3 rounded-xl bg-white/5 text-blue-200 font-bold text-center hover:bg-blue-600 hover:text-white transition">
                    {{ __('Komunikasi Real-Time') }}
                </a>
                <a href="#queue" @click="mobileMenuOpen = false" class="p-3 rounded-xl bg-white/5 text-blue-200 font-bold text-center hover:bg-blue-600 hover:text-white transition">
                    {{ __('Daftar Antrean Tiket') }}
                </a>
                <a href="#departments-list" @click="mobileMenuOpen = false" class="p-3 rounded-xl bg-white/5 text-blue-200 font-bold text-center hover:bg-blue-600 hover:text-white transition">
                    {{ __('Direktori Departemen') }}
                </a>
                @if (auth()->check() && ((int) auth()->user()->department_id === 2 || (int) auth()->user()->department_id === 4 || str_contains(strtolower(auth()->user()->department?->name ?? ''), 'hr')))
                    <a href="{{ url('/hcm-core/employees-master') }}" @click="mobileMenuOpen = false" class="col-span-2 sm:col-span-5 p-3 rounded-xl bg-blue-600 text-white font-bold text-center hover:bg-blue-500 transition shadow-md">
                        {{ __('🛡️ Buka Vault HRD (Upload CSV & Master Data Karyawan)') }}
                    </a>
                @endif

                <!-- Mobile Language Selector Row -->
                <div class="col-span-2 sm:col-span-5 p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 text-center">{{ __('Pilih Bahasa') }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($languages as $code => $lang)
                            <a href="{{ route('locale.switch', $code) }}" 
                               class="py-2 px-2.5 rounded-lg text-center flex items-center justify-center gap-1.5 text-xs font-semibold transition {{ $currentLocale === $code ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-white/10 text-slate-200 hover:bg-white/20' }}">
                                <span class="text-sm leading-none">{{ $lang['flag'] }}</span>
                                <span>{{ $lang['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <button type="button" @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }" 
                    class="col-span-2 sm:col-span-5 p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-center transition flex items-center justify-center gap-2 cursor-pointer border border-white/15">
                    <span x-show="darkMode" class="flex items-center gap-2 text-amber-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>{{ __('Ganti ke Mode Terang (Light Mode)') }}</span>
                    </span>
                    <span x-show="!darkMode" class="flex items-center gap-2 text-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <span>{{ __('Ganti ke Mode Gelap (Dark Mode)') }}</span>
                    </span>
                </button>
            </div>

            <!-- HERO CENTER CONTENT (Exact typography and vertical guide-line from screenshot) -->
            <div class="pt-8 pb-16 md:pt-12 md:pb-24">
                <div class="max-w-3xl">
                    <!-- Vertical Line with Top Dot Accent -->
                    <div class="relative pl-7 border-l-2 border-white/85 py-1">
                        <!-- Top Circle Dot -->
                        <span class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-white shadow-md"></span>

                        <!-- Main Giant Headline from Screenshot -->
                        <h1 class="text-3xl sm:text-5xl md:text-6xl font-black text-white tracking-tight uppercase leading-[1.08] font-sans">
                            @if(app()->getLocale() === 'en')
                                PLASTIC<br>
                                PACKAGING<br>
                                MANUFACTURING<br>
                                ENTERPRISE
                            @elseif(app()->getLocale() === 'ja')
                                プラスチック<br>
                                包装資材<br>
                                製造企業
                            @elseif(app()->getLocale() === 'zh')
                                塑料包装<br>
                                制造企业
                            @else
                                PERUSAHAAN<br>
                                MANUFAKTUR<br>
                                PENGEMASAN<br>
                                PLASTIK
                            @endif
                        </h1>

                        <!-- Subtitle from Screenshot -->
                        <div class="mt-5 text-white tracking-wider text-xs sm:text-sm font-extrabold uppercase space-y-0.5">
                            <p class="text-white/95">{{ __('KAMI ADALAH AHLI') }}</p>
                            <p class="text-blue-300">{{ __('DALAM INJECTION DAN BLOW MOLDING') }}</p>
                        </div>
                    </div>

                    <!-- System Portal Headline & Fast Action Buttons -->
                    <div class="mt-8 pl-7 flex flex-wrap items-center gap-3">
                        <div class="w-full text-xs text-blue-200 font-medium mb-1">
                            {{ __('Sistem Ticketing & Komunikasi Antar-Departemen Terpadu') }}
                        </div>
                        <a href="#new-ticket" 
                           class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>{{ __('Buat Tiket Dukungan') }}</span>
                        </a>
                        <a href="#chat-pane" 
                           class="px-5 py-3 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white font-bold text-xs uppercase tracking-wider border border-white/25 transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>{{ __('Live Chat Desk') }}</span>
                        </a>
                        <a href="#queue" 
                           class="px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white/90 font-semibold text-xs tracking-wider border border-white/15 transition cursor-pointer">
                            {{ __('Antrean Tiket') }} ({{ \App\Models\Ticket::count() }})
                        </a>
                        <a href="#global-tickets" 
                           class="px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-blue-200 hover:text-white font-semibold text-xs tracking-wider border border-white/15 transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            <span>{{ __('Tiket Global') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN BODY CONTENT AREA (Split-Screen & Desk Dashboard with clean spacing, no negative margins) -->
    <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-20">
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
