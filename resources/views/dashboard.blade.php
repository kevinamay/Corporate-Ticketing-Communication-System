@extends('layouts.app')

@section('content')
@php
    $activeUser = auth()->user();
    if (!$activeUser && session('active_user_id')) {
        $activeUser = \App\Models\User::find(session('active_user_id'));
        if ($activeUser) {
            auth()->login($activeUser);
        }
    }
    $isAdmin = $activeUser && (
        $activeUser->role === 'admin' ||
        $activeUser->email === 'user123@gmail.com'
    );
    $canAccessMasterData = $activeUser && ($activeUser->email === 'user123@gmail.com');
@endphp

<div class="space-y-6">

    @if ($isAdmin)
        <!-- ==================================================================== -->
        <!-- HALAMAN ADMIN & HRD: INPUT DATA KARYAWAN & MENJAWAB TIKET MASUK -->
        <!-- ==================================================================== -->

        <!-- Admin Role Welcome & Security Clearance Banner -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white shadow-lg border border-blue-700/40 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/30 text-blue-200 border border-blue-400/30">
                            {{ __('Portal Admin IT') }}
                        </span>
                        <span class="text-xs text-blue-300">&bull; {{ $activeUser->name }}</span>
                    </div>
                    <h2 class="text-lg font-black tracking-tight mt-0.5">{{ __('Manajemen Data Karyawan & Penanganan Tiket Masuk') }}</h2>
                    <p class="text-xs text-blue-200/80">{{ __('Kelola otorisasi data karyawan (CRUD & CSV) serta jawab dan tangani tiket kendala yang masuk dari seluruh divisi.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if ($canAccessMasterData)
                    <a href="#hcm-master-section" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm">
                        {{ __('+ Input Karyawan') }}
                    </a>
                @endif
                <a href="#global-tickets" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20">
                    {{ __('Jawab Tiket Masuk') }}
                </a>
            </div>
        </div>

        <!-- Elevated KPI Metrics Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Tiket Aktif -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Tiket Masuk Aktif') }}</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>

            <!-- 2. Menunggu Penanganan -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Perlu Dijawab') }}</p>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ \App\Models\Ticket::where('status', 'Pending')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- 3. Sedang Diproses -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Sedang Dikerjakan') }}</p>
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ \App\Models\Ticket::where('status', 'In Progress')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
            </div>

            <!-- 4. Tiket Selesai -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Terselesaikan') }}</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ \App\Models\Ticket::where('status', 'Resolved')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        @if ($canAccessMasterData)
            <!-- 1. MODUL INPUT DATA KARYAWAN (Khusus Admin IT: user123@gmail.com) -->
            <div id="hcm-master-section" class="scroll-mt-24">
                <livewire:hcm-employee-master />
            </div>
        @endif

        <!-- 2. MODUL MENJAWAB TIKET MASUK (Global Tickets Answering & Processing) -->
        <div id="global-tickets" class="scroll-mt-24 mt-8">
            <livewire:global-tickets />
        </div>

    @else
        <!-- ==================================================================== -->
        <!-- HALAMAN KARYAWAN: HALAMAN TICKETING (SUBMIT REQUEST & ANTREAN TIKET) -->
        <!-- ==================================================================== -->

        <!-- Karyawan Welcome & Guidance Banner -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white shadow-lg border border-blue-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-600/30 border border-blue-500/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-600/40 text-blue-200 border border-blue-400/30">
                            {{ __('Portal Layanan Tiket Karyawan') }}
                        </span>
                        <span class="text-xs text-blue-300">&bull; {{ $activeUser?->name ?? __('Karyawan') }}</span>
                    </div>
                    <h2 class="text-lg font-black tracking-tight mt-0.5">{{ __('Pengajuan Kendala & Bantuan Teknis') }}</h2>
                    <p class="text-xs text-slate-300">{{ __('Laporkan kendala operasional ke tim IT Support dan pantau status penyelesaian tiket secara real-time.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="#new-ticket" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>{{ __('Buat Tiket Baru') }}</span>
                </a>
                <a href="#queue" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20">
                    {{ __('Daftar Tiket') }}
                </a>
            </div>
        </div>

        <!-- Elevated KPI Metrics Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Tiket Aktif -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Tiket Aktif') }}</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>

            <!-- 2. Menunggu Penanganan -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Menunggu Penanganan') }}</p>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ \App\Models\Ticket::where('status', 'Pending')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- 3. Sedang Diproses -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Sedang Dikerjakan') }}</p>
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ \App\Models\Ticket::where('status', 'In Progress')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
            </div>

            <!-- 4. Tiket Selesai -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Terselesaikan') }}</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ \App\Models\Ticket::where('status', 'Resolved')->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Department Quick Navigation Ribbon -->
        <div id="departments-list" class="scroll-mt-24 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm p-4 flex flex-col md:flex-row items-center justify-between gap-4 transition-colors">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Unit Operasional:') }}</span>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach (\App\Models\Department::all() as $dept)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ $dept->name }}
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">({{ $dept->tickets()->count() }})</span>
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-3">
                <span>{{ __('SLA Standar Pabrik:') }} <strong class="text-slate-700 dark:text-slate-200">&lt; 15 Menit</strong></span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">&bull; {{ __('Online 24/7') }}</span>
            </div>
        </div>

        <!-- 1. Form Pengajuan Tiket Dukungan (IT Support) -->
        <div id="new-ticket" class="scroll-mt-24">
            <livewire:ticket-form />
        </div>

        <!-- 2. Antrean Tiket & Status Pengerjaan (TicketList Component) -->
        <div id="queue" class="scroll-mt-24 mt-8">
            <livewire:ticket-list />
        </div>
    @endif

</div>
@endsection
