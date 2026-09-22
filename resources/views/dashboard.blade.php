@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Metrics / Status Bar Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="clay-card p-4 bg-white flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Tickets</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ \App\Models\Ticket::where('status', '!=', 'Resolved')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>

        <div class="clay-card p-4 bg-white flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pending Review</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ \App\Models\Ticket::where('status', 'Pending')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="clay-card p-4 bg-white flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">In Progress</p>
                <p class="text-2xl font-black text-purple-600 mt-1">{{ \App\Models\Ticket::where('status', 'In Progress')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
        </div>

        <div class="clay-card p-4 bg-white flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Resolved</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ \App\Models\Ticket::where('status', 'Resolved')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Split-Screen Dashboard: Left Ticket Form & Right Real-Time Chat -->
    <div id="split-screen" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left/Center Card: Ticket Creation Component (7 Columns) -->
        <div id="new-ticket" class="lg:col-span-7">
            <livewire:ticket-form />
        </div>

        <!-- Right Side Panel: Real-Time Communication / Chat Component (5 Columns) -->
        <div id="chat-pane" class="lg:col-span-5 h-full">
            <livewire:ticket-chat />
        </div>
    </div>

    <!-- Interactive Ticket Queue List -->
    <div id="queue" class="mt-8">
        <livewire:ticket-list />
    </div>
</div>
@endsection
