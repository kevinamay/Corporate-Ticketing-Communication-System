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
<body class="h-full font-sans antialiased text-slate-800 bg-[#f8f9fa]">
    <div class="min-h-full flex flex-col">
        <!-- Modern Top Header -->
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/70 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
                <!-- Company Logo on Left -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-pink-400 via-pink-300 to-rose-300 flex items-center justify-center text-white shadow-md shadow-pink-300/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xl tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-pink-600 bg-clip-text text-transparent">NEXUSTICKET</span>
                            <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">Enterprise</span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium">Corporate Communication & Ticketing Hub</p>
                    </div>
                </div>

                <!-- Right Side: Notification Bell with Red Dot & User Profile Picture / Username -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell Icon with Red Dot for unread alerts -->
                    <div class="relative" title="Unread Notifications">
                        <button class="clay-button p-2.5 rounded-2xl bg-white text-slate-600 hover:text-pink-600 cursor-pointer flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </button>
                        <!-- Red Dot for Unread Alerts -->
                        <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                    </div>

                    <!-- User Identity, Profile Picture, and Username -->
                    <livewire:user-switcher />
                </div>
            </div>
        </header>

        <!-- Main Body Layout with Sidebar -->
        <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row gap-6">
            <!-- Sidebar Navigation -->
            <aside class="w-full md:w-64 shrink-0 space-y-5">
                <nav class="clay-card p-3 bg-white space-y-1">
                    <!-- 1. Dashboard -->
                    <a href="{{ url('/') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs bg-pink-50 text-pink-700 shadow-xs transition">
                        <svg class="w-4 h-4 text-pink-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- 2. My Tickets -->
                    <a href="#queue" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>Dept Tickets</span>
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-pink-100 text-pink-700">
                            {{ \App\Models\Ticket::count() }}
                        </span>
                    </a>

                    <!-- 4. New Request -->
                    <a href="#new-ticket" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>New Request</span>
                    </a>

                    <!-- 5. Team Chat -->
                    <a href="#chat-pane" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>Team Chat</span>
                    </a>
                </nav>

                <!-- Department Quick Directory Card -->
                <div class="clay-card p-4 bg-white">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Departments</h4>
                    <div class="space-y-2">
                        @foreach (\App\Models\Department::all() as $dept)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                                <span class="font-semibold text-slate-700 truncate">{{ $dept->name }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">
                                    {{ $dept->tickets()->count() }} tickets
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SLA & Support Status Card -->
                <div class="clay-card p-4 bg-gradient-to-br from-pink-50/80 to-rose-50/50 border border-pink-100/60">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-pink-900">SLA Performance</p>
                        <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-pink-200 text-pink-800">98.4%</span>
                    </div>
                    <p class="text-[11px] text-pink-700 mt-1">Average response time under 8 mins</p>
                    <div class="w-full bg-pink-200/80 rounded-full h-1.5 mt-2.5 overflow-hidden">
                        <div class="bg-pink-400 h-1.5 rounded-full" style="width: 94%"></div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area (Slot & Section) -->
            <main class="flex-1 min-w-0">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
