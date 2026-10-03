<div @if(!$isDetailModalOpen) wire:poll.5s @endif class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-md p-5 sm:p-6 transition-colors">
    <!-- Header Strip with Icon & Badges -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-5 border-b border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/60 flex items-center justify-center shadow-xs">
                <!-- Checkmark in Document SVG -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                        @if ($isAdmin)
                            {{ __('Riwayat Tiket Selesai & Arsip Solusi (Semua Departemen)') }}
                        @else
                            {{ __('Riwayat Tiket Saya yang Selesai') }}
                        @endif
                    </h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        {{ $tickets->total() }} {{ __('Selesai') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ __('Realtime Auto-Sync') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    @if ($isAdmin)
                        {{ __('Arsip lengkap seluruh tiket antar-divisi yang telah berhasil diselesaikan oleh tim dukungan dan departemen tujuan.') }}
                    @else
                        {{ __('Daftar riwayat tiket yang Anda ajukan yang telah selesai diproses. Anda dapat memeriksa hasil pengerjaan dan solusi penanganan.') }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Scope Tabs for Admin: Semua Tiket Selesai vs Tiket Saya Selesai -->
        @if ($isAdmin)
            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-xs">
                <button type="button" wire:click="$set('scopeFilter', 'all')" 
                    class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $scopeFilter === 'all' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    {{ __('Semua Riwayat') }} ({{ $totalResolvedAll }})
                </button>
                <button type="button" wire:click="$set('scopeFilter', 'mine')" 
                    class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer {{ $scopeFilter === 'mine' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    {{ __('Tiket Saya Selesai') }} ({{ $totalResolvedMine }})
                </button>
            </div>
        @endif
    </div>

    <!-- Search & Filter Controls Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 mb-5">
        <!-- Search Input -->
        <div class="lg:col-span-5 relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="{{ __('Cari riwayat tiket, judul, ID tiket, atau solusi...') }}"
                   class="w-full pl-9 pr-3.5 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-2xs">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Target Department Filter -->
        <div class="lg:col-span-4">
            <select wire:model.live="departmentFilter" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition shadow-2xs">
                <option value="all">{{ __('Semua Departemen Penangan') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ __($dept->name) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sort Order -->
        <div class="lg:col-span-3">
            <select wire:model.live="sortBy" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition shadow-2xs">
                <option value="latest">{{ __('Terbaru Selesai') }}</option>
                <option value="oldest">{{ __('Terlama Selesai') }}</option>
            </select>
        </div>
    </div>

    @if ($isAdmin)
        <!-- ===================================================================== -->
        <!-- ADMIN VIEW: DATA TABLE OF COMPLETED TICKETS                           -->
        <!-- ===================================================================== -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                    <tr>
                        <th scope="col" class="py-3.5 px-3.5">{{ __('Ticket ID') }}</th>
                        <th scope="col" class="py-3.5 px-3.5">{{ __('Judul & Pelapor') }}</th>
                        <th scope="col" class="py-3.5 px-3.5">{{ __('Departemen Penangan') }}</th>
                        <th scope="col" class="py-3.5 px-3.5">{{ __('Kategori') }}</th>
                        <th scope="col" class="py-3.5 px-3.5">{{ __('Waktu & Durasi') }}</th>
                        <th scope="col" class="py-3.5 px-3.5">{{ __('Status') }}</th>
                        <th scope="col" class="py-3.5 px-3.5 text-right">{{ __('Solusi & Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                    @forelse ($tickets as $ticket)
                        @php
                            $durationText = $ticket->created_at && $ticket->updated_at 
                                ? $ticket->created_at->diffForHumans($ticket->updated_at, true) 
                                : '-';
                            $lastMessage = $ticket->messages->last();
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition">
                            <!-- Ticket ID -->
                            <td class="py-3.5 px-3.5 font-mono font-bold text-slate-600 dark:text-slate-400">
                                #{{ $ticket->id }}
                            </td>

                            <!-- Judul & Pelapor -->
                            <td class="py-3.5 px-3.5">
                                <button type="button" wire:click="viewDetail({{ $ticket->id }})" 
                                    class="text-left font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition cursor-pointer max-w-xs truncate block"
                                    title="{{ $ticket->title }}">
                                    {{ $ticket->title }}
                                </button>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $ticket->sender?->name ?? __('Anonim') }}</span>
                                    @if ($ticket->sender?->department)
                                        <span>&bull; {{ __($ticket->sender->department->name) }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Departemen Penangan -->
                            <td class="py-3.5 px-3.5 font-medium text-slate-800 dark:text-slate-200">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __($ticket->targetDepartment?->name ?? '-') }}
                                </span>
                            </td>

                            <!-- Kategori & Prioritas -->
                            <td class="py-3.5 px-3.5">
                                <span class="font-semibold text-slate-700 dark:text-slate-300 block text-[11px]">{{ $ticket->category ?: __('Umum') }}</span>
                                <span class="text-[10px] text-slate-400">{{ __('Prioritas:') }} {{ $ticket->priority ?: 'Medium' }}</span>
                            </td>

                            <!-- Waktu Masuk, Selesai & Durasi -->
                            <td class="py-3.5 px-3.5 text-[11px] text-slate-500 dark:text-slate-400">
                                <div class="font-mono text-slate-700 dark:text-slate-300">
                                    {{ $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : '-' }}
                                </div>
                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('Selesai dlm') }} {{ $durationText }}
                                </span>
                            </td>

                            <!-- Status Selesai Badge -->
                            <td class="py-3.5 px-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800">
                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    {{ __('Terselesaikan') }}
                                </span>
                            </td>

                            <!-- Solusi & Aksi Detail -->
                            <td class="py-3.5 px-3.5 text-right">
                                <button type="button" wire:click="viewDetail({{ $ticket->id }})" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 font-bold text-xs transition border border-emerald-200 dark:border-emerald-800/60 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>{{ __('Lihat Riwayat & Solusi') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ __('Belum Ada Riwayat Tiket Selesai') }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">
                                    {{ __('Tiket yang telah ditandai selesai (Resolved) oleh admin atau departemen penangan akan otomatis terarsip di sini.') }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        <!-- ===================================================================== -->
        <!-- EMPLOYEE VIEW: CARDS GRID OF COMPLETED TICKETS                         -->
        <!-- ===================================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4">
            @forelse ($tickets as $t)
                @php
                    $durationText = $t->created_at && $t->updated_at 
                        ? $t->created_at->diffForHumans($t->updated_at, true) 
                        : '-';
                    $lastMessage = $t->messages->last();
                @endphp
                <div class="p-4 rounded-xl transition border text-left bg-white dark:bg-slate-800/80 border-gray-200 dark:border-slate-700/80 hover:border-emerald-400 dark:hover:border-emerald-500 hover:shadow-md flex flex-col justify-between group">
                    <div>
                        <!-- Top Meta: Timestamp, Badge ID -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 font-mono flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>{{ $t->updated_at ? $t->updated_at->format('d/m/Y H:i') : '' }}</span>
                                </span>
                                @if ($t->photo_path)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-1.5 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>{{ __('Foto') }}</span>
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono font-bold">#{{ $t->id }}</span>
                        </div>

                        <!-- Title (Clickable) -->
                        <button type="button" 
                                wire:click="viewDetail({{ $t->id }})" 
                                class="text-left font-bold text-xs text-slate-900 dark:text-white line-clamp-1 mb-1 hover:text-emerald-600 dark:hover:text-emerald-400 transition cursor-pointer w-full"
                                title="{{ $t->title }}">
                            {{ $t->title }}
                        </button>

                        <!-- Description Preview -->
                        <p wire:click="viewDetail({{ $t->id }})" 
                           class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mb-3 cursor-pointer hover:text-slate-700 dark:hover:text-slate-300">
                            {{ strip_tags($t->formatted_description ?? $t->description) }}
                        </p>

                        <!-- Solution Summary Snippet -->
                        @if ($lastMessage)
                            <div class="mb-3 p-2.5 rounded-lg bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 text-[11px]">
                                <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-800 dark:text-emerald-300 mb-0.5">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>{{ __('Solusi / Tanggapan:') }}</span>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 line-clamp-2 italic">
                                    "{{ Str::limit(strip_tags($lastMessage->message), 100) }}"
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Section: Target Dept, Status & Actions -->
                    <div class="pt-2.5 border-t border-gray-100 dark:border-slate-700/50 space-y-2">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500">
                            <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[130px]">
                                {{ __($t->targetDepartment?->name ?? '-') }}
                            </span>
                            
                            <!-- Status Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border font-bold text-[10px] bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                {{ __('Terselesaikan') }}
                            </span>
                        </div>

                        <!-- Read Solution Action Button -->
                        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-1 pt-1 border-t border-gray-50 dark:border-slate-800 text-[11px]">
                            <span class="text-[10px] text-slate-400">
                                {{ __('Selesai dlm') }} {{ $durationText }}
                            </span>
                            <button type="button" 
                                    wire:click="viewDetail({{ $t->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>{{ __('Detail Riwayat & Solusi') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 px-4 rounded-xl border-2 border-dashed border-gray-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ __('Belum Ada Riwayat Tiket Selesai') }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">
                        {{ __('Tiket yang telah selesai ditangani akan otomatis muncul di bagian riwayat ini.') }}
                    </p>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Pagination Footer -->
    @if ($tickets->hasPages())
        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-slate-800">
            {{ $tickets->links() }}
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- READ MODAL: DETAIL LENGKAP RIWAYAT TIKET & SOLUSI PENANGANAN               -->
    <!-- ========================================================================= -->
    @if ($isDetailModalOpen && $viewingTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 animate-fade-in"
             x-data 
             @keydown.escape.window="$wire.closeDetailModal()">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all max-h-[92vh] flex flex-col">
                <!-- Modal Top Header -->
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-800/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 font-mono font-black text-xs flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                            #{{ $viewingTicket->id }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base leading-snug">
                                    {{ $viewingTicket->title }}
                                </h3>
                            </div>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ __('Arsip Dokumen Riwayat & Solusi Penanganan Tiket') }}
                            </span>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="w-8 h-8 rounded-lg bg-slate-200/80 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Content Scroll Area -->
                <div class="p-5 sm:p-6 overflow-y-auto space-y-5 text-xs">
                    <!-- Status Banner -->
                    <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="font-black text-xs">{{ __('Tiket Telah Selesai Ditangani (Resolved)') }}</p>
                                <p class="text-[11px] text-emerald-700 dark:text-emerald-300">
                                    {{ __('Laporan kendala ini telah rampung diperbaiki dan ditutup secara resmi.') }}
                                </p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white font-black text-[10px] tracking-wider uppercase shrink-0">
                            {{ __('Terselesaikan') }}
                        </span>
                    </div>

                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-800">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Pelapor') }}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $viewingTicket->sender?->name ?? __('Anonim') }}</span>
                            <span class="text-[10px] text-slate-500 block">{{ __($viewingTicket->sender?->department?->name ?? '-') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Ditangani Oleh') }}</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-xs">{{ __($viewingTicket->targetDepartment?->name ?? '-') }}</span>
                            <span class="text-[10px] text-slate-500 block">{{ __('Departemen Tujuan') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Waktu Pengajuan') }}</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300 text-[11px] block">{{ $viewingTicket->created_at ? $viewingTicket->created_at->format('d/m/Y H:i') : '-' }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mt-1">{{ __('Waktu Selesai') }}</span>
                            <span class="font-mono text-emerald-600 dark:text-emerald-400 text-[11px] font-bold block">{{ $viewingTicket->updated_at ? $viewingTicket->updated_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Durasi Pengerjaan') }}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-100 text-xs block">
                                {{ $viewingTicket->created_at && $viewingTicket->updated_at ? $viewingTicket->created_at->diffForHumans($viewingTicket->updated_at, true) : '-' }}
                            </span>
                            <span class="text-[10px] text-slate-500 block">{{ __('Kategori: ') . ($viewingTicket->category ?: 'Umum') }}</span>
                        </div>
                    </div>

                    <!-- Masalah Awal yang Dilaporkan -->
                    <div class="space-y-2">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Rincian Laporan Kendala Awal') }}</span>
                        </h4>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-gray-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 leading-relaxed text-xs">
                            {!! $viewingTicket->formatted_description ?: nl2br(e($viewingTicket->description)) !!}
                        </div>

                        <!-- Bukti Foto Awal -->
                        @if ($viewingTicket->photo_url)
                            <div class="mt-3">
                                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 block mb-1.5">{{ __('Bukti Foto Kendala dari Pelapor:') }}</span>
                                <a href="{{ $viewingTicket->photo_url }}" target="_blank" class="inline-block group relative rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 max-w-xs shadow-xs hover:border-blue-500 transition">
                                    <img src="{{ $viewingTicket->photo_url }}" alt="Bukti Foto" class="max-h-48 w-auto object-cover group-hover:scale-102 transition duration-200">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white font-bold text-xs transition">
                                        {{ __('Klik Perbesar Foto') }}
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Riwayat Solusi & Diskusi Penyelesaian -->
                    <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>{{ __('Riwayat Solusi & Tanggapan Teknisi / Admin') }}</span>
                            <span class="text-slate-400 font-normal">({{ $viewingTicket->messages->count() }} {{ __('pesan') }})</span>
                        </h4>

                        @if ($viewingTicket->messages->isEmpty())
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-dashed border-gray-200 dark:border-slate-800 text-center text-slate-500 text-xs">
                                {{ __('Tiket ini diselesaikan langsung oleh penangan tanpa catatan percakapan tambahan.') }}
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($viewingTicket->messages as $index => $msg)
                                    @php
                                        $isStaffResponse = $msg->user && ($msg->user->role === 'admin' || $msg->user->email === 'user123@gmail.com' || (int)$msg->user->department_id === (int)$viewingTicket->target_department_id);
                                    @endphp
                                    <div class="p-3.5 rounded-xl border {{ $isStaffResponse ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : 'bg-slate-50 dark:bg-slate-800/50 border-gray-200 dark:border-slate-700/60' }}">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs {{ $isStaffResponse ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-800 dark:text-slate-200' }}">
                                                    {{ $msg->user?->name ?? __('Pengguna') }}
                                                </span>
                                                @if ($isStaffResponse)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-600 text-white">
                                                        {{ __('Teknisi / Admin') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                {{ $msg->created_at ? $msg->created_at->format('d/m/Y H:i') : '' }}
                                            </span>
                                        </div>
                                        <div class="text-slate-700 dark:text-slate-300 text-xs leading-relaxed">
                                            {!! $msg->formatted_message ?: nl2br(e($msg->message)) !!}
                                        </div>

                                        @if ($msg->photo_url)
                                            <div class="mt-2.5">
                                                <a href="{{ $msg->photo_url }}" target="_blank" class="inline-block group rounded-lg overflow-hidden border border-gray-200 dark:border-slate-700 max-w-xs">
                                                    <img src="{{ $msg->photo_url }}" alt="Lampiran Solusi" class="max-h-36 w-auto object-cover">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Bottom Action Strip -->
                <div class="px-5 sm:px-6 py-3.5 border-t border-gray-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                        {{ __('Status:') }} <strong class="text-emerald-600 dark:text-emerald-400">RESOLVED</strong>
                    </span>
                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition cursor-pointer">
                        {{ __('Tutup Riwayat') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
