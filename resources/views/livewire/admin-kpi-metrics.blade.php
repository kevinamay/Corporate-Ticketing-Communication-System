<div wire:poll.5s class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
    <!-- 1. Tiket Aktif -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-3 sm:p-4 flex items-center justify-between transition-colors">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">{{ __('Tiket Masuk Aktif') }}</p>
            <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-0.5 sm:mt-1">{{ $activeCount }}</p>
        </div>
        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
    </div>

    <!-- 2. Menunggu Penanganan -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-3 sm:p-4 flex items-center justify-between transition-colors">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">{{ __('Perlu Dijawab') }}</p>
            <p class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5 sm:mt-1">{{ $pendingCount }}</p>
        </div>
        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>

    <!-- 3. Sedang Diproses -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-3 sm:p-4 flex items-center justify-between transition-colors">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">{{ __('Sedang Dikerjakan') }}</p>
            <p class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5 sm:mt-1">{{ $inProgressCount }}</p>
        </div>
        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
    </div>

    <!-- 4. Tiket Selesai -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-md p-3 sm:p-4 flex items-center justify-between transition-colors">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">{{ __('Terselesaikan') }}</p>
            <p class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 sm:mt-1">{{ $resolvedCount }}</p>
        </div>
        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
</div>
