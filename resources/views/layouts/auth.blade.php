<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Autentikasi' }} - PT. Asia Plastik System Portal</title>

    <!-- Theme Initialization Script to avoid FOUC (Flash of Unstyled Content) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
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
<body class="h-full font-sans antialiased text-slate-800 dark:text-slate-100 bg-slate-900 transition-colors"
      x-data="{ darkMode: document.documentElement.classList.contains('dark') }">
    <!-- Floating Dark Mode & Language Switcher on Auth pages -->
    <div class="fixed top-4 right-4 z-50 flex items-center gap-2">
        <!-- Multi-Language Dropdown Selector -->
        <div class="relative" x-data="{ langOpen: false }" @click.away="langOpen = false">
            <button type="button" @click="langOpen = !langOpen" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white border border-white/25 shadow-lg backdrop-blur-md transition cursor-pointer text-xs font-bold select-none"
                title="{{ __('Pilih Bahasa') }}">
                <span class="text-sm leading-none">{{ $currentLang['flag'] }}</span>
                <span>{{ $currentLang['short'] }}</span>
                <svg class="w-3.5 h-3.5 text-white/70 transition-transform duration-200" :class="{ 'rotate-180': langOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <button type="button" @click="darkMode = !darkMode; if (darkMode) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }" 
            class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white border border-white/25 shadow-lg backdrop-blur-md transition cursor-pointer text-xs font-bold"
            :title="darkMode ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
            <span x-show="darkMode" class="flex items-center gap-1.5 text-amber-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>{{ __('Mode Terang') }}</span>
            </span>
            <span x-show="!darkMode" class="flex items-center gap-1.5 text-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <span>{{ __('Mode Gelap') }}</span>
            </span>
        </button>
    </div>

    {{ $slot ?? '' }}
    @yield('content')

    @livewireScripts
</body>
</html>
