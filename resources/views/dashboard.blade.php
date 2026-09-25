@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Prominent Direct Access for HR Department (The Vault - Master Data & CSV Upload) -->
    @if (auth()->check() && ((int) auth()->user()->department_id === 2 || (int) auth()->user()->department_id === 4 || str_contains(strtolower(auth()->user()->department?->name ?? ''), 'hr')))
        <div class="w-full bg-gradient-to-r from-blue-900 via-blue-800 to-blue-950 rounded-2xl p-5 sm:p-6 text-white shadow-xl border border-blue-600/40 relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                <!-- Left Details (Takes 8-9 cols on desktop) -->
                <div class="lg:col-span-8 xl:col-span-9 flex items-start sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-600/80 border border-blue-400/40 flex items-center justify-center shrink-0 shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-500/30 text-blue-200 border border-blue-400/30">
                                {{ __('Modul Khusus HRD') }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/30 text-emerald-200 border border-emerald-400/30">
                                {{ __('The Vault') }}
                            </span>
                        </div>
                        <h2 class="text-base sm:text-lg font-black text-white tracking-tight">
                            {{ __('HCM Employee Master Data & Bulk Import CSV') }}
                        </h2>
                        <p class="text-xs text-blue-100/90 leading-relaxed">
                            {{ __('Kelola data resmi karyawan, tambah manual karyawan baru, dan Import CSV untuk sinkronisasi NIK pendaftaran akun sistem.') }}
                        </p>
                    </div>
                </div>

                <!-- Right Action Button (Takes 3-4 cols on desktop) -->
                <div class="lg:col-span-4 xl:col-span-3 flex lg:justify-end">
                    <a href="{{ url('/hcm-core/employees-master') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-blue-950/40 border border-blue-400/40 transition cursor-pointer">
                        <span>{{ __('Buka Menu Import CSV') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Elevated KPI Metrics Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Tiket Aktif -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Tiket Aktif') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>

        <!-- 2. Menunggu Penanganan -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Menunggu Penanganan') }}</p>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ \App\Models\Ticket::where('status', 'Pending')->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/60 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- 3. Sedang Diproses -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Sedang Dikerjakan') }}</p>
                <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ \App\Models\Ticket::where('status', 'In Progress')->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
        </div>

        <!-- 4. Tiket Selesai -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-4 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Terselesaikan') }}</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ \App\Models\Ticket::where('status', 'Resolved')->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/60 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
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

    <!-- Main Split-Screen Console: Left Form & Right Livewire Chat -->
    <div id="split-screen" class="scroll-mt-24 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left/Center Card: Ticket Creation Component (7 Columns) -->
        <div id="new-ticket" class="scroll-mt-24 lg:col-span-7">
            <livewire:ticket-form />
        </div>

        <!-- Right Side Panel: Real-Time Communication / Chat Component (5 Columns) -->
        <div id="chat-pane" class="scroll-mt-24 lg:col-span-5 h-full">
            <livewire:ticket-chat />
        </div>
    </div>

    <!-- Active Incident & Request Queue (TicketList Component) -->
    <div id="queue" class="scroll-mt-24 mt-8">
        <livewire:ticket-list />
    </div>

    <!-- Global Tickets (Cross-Department Transparency & Department-Restricted Actions) -->
    <div id="global-tickets" class="scroll-mt-24 mt-8">
        <livewire:global-tickets />
    </div>
</div>
@endsection
