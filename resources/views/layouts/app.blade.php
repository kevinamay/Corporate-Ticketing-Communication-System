<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Corporate Ticketing & Communication System') }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col">

    <!-- Prominent Top Header / Hero Section with Company Building Photo -->
    <header class="relative shadow-md overflow-hidden" 
            style="background-image: url('/images/fotopt_2.webp'); background-size: cover; background-position: center;">
        
        <!-- Strong Dark Blue / Industrial Gradient Overlay for High Readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-950/92 via-blue-900/85 to-slate-900/88 pointer-events-none"></div>

        <!-- Relative Container for Header Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Navigation Bar (Placed over the hero image) -->
            <div class="h-20 flex items-center justify-between border-b border-white/10">
                <!-- Left: Company Logo -->
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 border border-blue-400/40 flex items-center justify-center text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xl tracking-tight text-white">NEXUS ENTERPRISE</span>
                            <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded bg-blue-500/30 text-blue-200 border border-blue-400/30">HQ Desk</span>
                        </div>
                        <p class="text-[11px] text-blue-200/80 font-medium">Ticketing & Cross-Departmental Operations</p>
                    </div>
                </div>

                <!-- Right: Notification Bell & User Profile -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell Icon with Red Dot -->
                    <div class="relative" title="Unread System Alerts">
                        <button class="p-2 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 text-white transition cursor-pointer flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </button>
                        <!-- Red Dot for Unread Alerts -->
                        <span class="absolute top-1 right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                    </div>

                    <!-- User Identity & Profile Switcher -->
                    <livewire:user-switcher />
                </div>
            </div>

            <!-- Hero Headline & Subheadline Section -->
            <div class="py-10 md:py-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-200 border border-blue-400/30 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        Enterprise Coordination Console
                    </div>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-white tracking-tight">
                        Corporate Ticketing & Communication Hub
                    </h1>
                    <p class="mt-2 text-sm md:text-base text-blue-100/90 font-normal max-w-2xl">
                        Streamlined inter-departmental support.
                    </p>
                </div>

                <!-- Quick Status Counters in Hero Banner -->
                <div class="flex items-center gap-3">
                    <div class="px-4 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-white">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">Open Incidents</span>
                        <span class="text-xl font-black">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</span>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-white">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">Departments</span>
                        <span class="text-xl font-black">{{ \App\Models\Department::count() }}</span>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-white">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">SLA Target</span>
                        <span class="text-xl font-black text-emerald-300">99.2%</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Body Layout with Sidebar Navigation (Below the hero section) -->
    <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
        <!-- Sidebar Navigation (Below the hero section on the left) -->
        <aside class="w-full md:w-64 shrink-0 space-y-6">
            <nav class="bg-white rounded-xl border border-gray-200 shadow-sm p-3 space-y-1">
                <!-- 1. Dashboard -->
                <a href="{{ url('/') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. My Tickets -->
                <a href="#queue" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Tickets</span>
                    </span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600">
                        {{ \App\Models\Ticket::where('sender_id', session('active_user_id', 1))->count() }}
                    </span>
                </a>

                <!-- 3. Dept Tickets -->
                <a href="#queue" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Dept Tickets</span>
                    </span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">
                        {{ \App\Models\Ticket::count() }}
                    </span>
                </a>

                <!-- 4. New Request (Highlighted in Light Corporate Blue) -->
                <a href="#new-ticket" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-xs transition">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>New Request</span>
                </a>

                <!-- 5. Team Chat -->
                <a href="#chat-pane" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Team Chat</span>
                </a>
            </nav>

            <!-- Department Directory List -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Enterprise Units</h4>
                <div class="space-y-2">
                    @foreach (\App\Models\Department::all() as $dept)
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-gray-100 text-xs">
                            <span class="font-medium text-slate-800 truncate">{{ $dept->name }}</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-white text-slate-600 border border-gray-200">
                                {{ $dept->tickets()->count() }} tickets
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Priority Emergency Hotline Banner -->
            <div class="p-4 rounded-xl bg-gradient-to-br from-slate-900 to-blue-950 text-white shadow-sm border border-blue-900">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">IT & Security Ops</span>
                </div>
                <p class="text-xs text-blue-200">24/7 Rapid Emergency Response Desk</p>
                <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-[11px] text-blue-300">
                    <span>VoIP Ext: <strong>1004</strong></span>
                    <span class="text-emerald-400 font-medium">Standby</span>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Corporate Footer -->
    <footer class="mt-auto border-t border-gray-200 bg-white py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Corporate Ticketing & Communication System. All rights reserved.</p>
            <div class="flex items-center gap-4 text-[11px] text-slate-500">
                <span>Enterprise Security SLA: Tier 3</span>
                <span>Encrypted AES-256</span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
