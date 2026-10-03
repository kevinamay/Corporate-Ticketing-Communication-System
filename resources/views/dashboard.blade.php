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


        <!-- Realtime Elevated KPI Metrics Strip (Auto-sync 2s) -->
        <livewire:admin-kpi-metrics />

        @if ($canAccessMasterData)
            <!-- 1. MODUL INPUT DATA KARYAWAN (Khusus Admin IT: user123@gmail.com) -->
            <div id="hcm-master-section" class="scroll-mt-24">
                <livewire:hcm-employee-master />
            </div>
        @endif

        <!-- 2. GRAFIK TREN REQUEST TIKET HARIAN (Chart Permintaan Tiket Per Hari) -->
        <div id="daily-ticket-chart-section" class="scroll-mt-24 mt-8">
            <livewire:ticket-daily-chart />
        </div>

        <!-- 3. MODUL MENJAWAB TIKET MASUK (Global Tickets Answering & Processing) -->
        <div id="global-tickets" class="scroll-mt-24 mt-8">
            <livewire:global-tickets />
        </div>

        <!-- 4. RIWAYAT TIKET SELESAI & ARSIP SOLUSI (Global Resolved Tickets History) -->
        <div id="admin-resolved-history" class="scroll-mt-24 mt-8">
            <livewire:resolved-ticket-history viewMode="admin" />
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

            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="#new-ticket" class="flex-1 sm:flex-initial justify-center px-3.5 sm:px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm flex items-center gap-1.5 text-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>{{ __('Buat Tiket Baru') }}</span>
                </a>
                <a href="#queue" class="flex-1 sm:flex-initial justify-center px-3 sm:px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 text-center">
                    {{ __('Daftar Tiket') }}
                </a>
                <a href="#resolved-history" class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-xl bg-emerald-600/80 hover:bg-emerald-600 text-white font-bold text-xs transition border border-emerald-400/30 flex items-center gap-1.5 shadow-sm text-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('Riwayat Tiket Selesai') }}</span>
                </a>
            </div>
        </div>

        <!-- Realtime Elevated KPI Metrics Strip (Employee View - Auto-sync 2s) -->
        <livewire:employee-kpi-metrics />


        <!-- 1. Form Pengajuan Tiket Dukungan (IT Support) -->
        <div id="new-ticket" class="scroll-mt-24">
            <livewire:ticket-form />
        </div>

        <!-- 2. Antrean Tiket & Status Pengerjaan (TicketList Component) -->
        <div id="queue" class="scroll-mt-24 mt-8">
            <livewire:ticket-list />
        </div>

        <!-- 3. Riwayat Tiket yang Sudah Terselesaikan (Employee Resolved Tickets History) -->
        <div id="resolved-history" class="scroll-mt-24 mt-8">
            <livewire:resolved-ticket-history viewMode="employee" />
        </div>
    @endif

</div>
@endsection
