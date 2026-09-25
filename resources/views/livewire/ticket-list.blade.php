<div class="bg-white dark:bg-slate-900 shadow-md border border-gray-200 dark:border-slate-800 rounded-xl p-5 mb-6 transition-colors" wire:poll.3s>
    
    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ __('Antrean Tiket & Insiden Aktif') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ __('Pilih atau klik tiket untuk membuka detail lengkap dan riwayat penanganan') }}</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="w-full sm:w-64">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari judul atau rincian tiket...') }}"
                class="w-full px-3 py-1.5 text-xs text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500" />
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('ticket_success'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2 animate-fade-in shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('ticket_success') }}</span>
        </div>
    @endif

    @if (session()->has('ticket_error'))
        <div class="mb-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2 animate-fade-in shadow-xs">
            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('ticket_error') }}</span>
        </div>
    @endif

    <!-- Filters Row -->
    <div class="flex flex-wrap items-center gap-2 mb-4 pb-3 border-b border-gray-100 dark:border-slate-800 text-xs">
        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('STATUS:') }}</span>
        <button wire:click="$set('statusFilter', 'all')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            {{ __('Semua') }}
        </button>
        <button wire:click="$set('statusFilter', 'Pending')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'Pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            {{ __('Pending') }}
        </button>
        <button wire:click="$set('statusFilter', 'Open')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'Open' ? 'bg-blue-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            {{ __('Open') }}
        </button>
        <button wire:click="$set('statusFilter', 'In Progress')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'In Progress' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            {{ __('In Progress') }}
        </button>
        <button wire:click="$set('statusFilter', 'Resolved')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'Resolved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            {{ __('Resolved') }}
        </button>

        <div class="h-4 w-[1px] bg-gray-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>

        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('DEPARTEMEN:') }}</span>
        <select wire:model.live="departmentFilter" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs text-slate-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700 focus:ring-1 focus:ring-blue-500 focus:outline-none">
            <option value="all">{{ __('Semua Departemen') }}</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Ticket Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse ($tickets as $t)
            @php
                $isOwner = $this->isOwner($t);
                $canModify = $this->canModifyTicket($t);
                $priorityBadge = match($t->priority) {
                    'Critical' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                    'High' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                    'Medium' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                    default => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                };
                $statusStyle = match($t->status) {
                    'Resolved' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                    'In Progress' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                    'Open' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                    default => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                };
            @endphp
            <div class="p-4 rounded-xl transition border text-left bg-white dark:bg-slate-800/80 border-gray-200 dark:border-slate-700/80 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md flex flex-col justify-between group">
                <div>
                    <!-- Top Meta: Priority, Photo, ID -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $priorityBadge }}">
                                {{ __($t->priority) }}
                            </span>
                            @if ($t->photo_path)
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-1.5 py-0.5 rounded border border-blue-200 dark:border-blue-800" title="{{ __('Ada Bukti Foto') }}">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>{{ __('Foto') }}</span>
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono font-bold">#{{ $t->id }}</span>
                    </div>

                    <!-- Title (Clickable to Read) -->
                    <button type="button" 
                            wire:click="viewTicket({{ $t->id }})" 
                            class="text-left font-bold text-xs text-slate-900 dark:text-white line-clamp-1 mb-1 hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer w-full"
                            title="{{ $t->title }}">
                        {{ $t->title }}
                    </button>

                    <!-- Description Preview (Clickable to Read) -->
                    <p wire:click="viewTicket({{ $t->id }})" 
                       class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mb-3 cursor-pointer hover:text-slate-700 dark:hover:text-slate-300">
                        {{ $t->description }}
                    </p>
                </div>

                <!-- Bottom Section: Target Dept, Status & Actions -->
                <div class="pt-2.5 border-t border-gray-100 dark:border-slate-700/50 space-y-2">
                    <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[120px]">
                            {{ $t->targetDepartment->name ?? '-' }}
                        </span>
                        
                        <!-- Status Badge -->
                        <span class="px-2 py-0.5 rounded border font-bold text-[10px] {{ $statusStyle }}">
                            @if ($t->status === 'Pending')
                                {{ __('Pending') }}
                            @else
                                {{ __($t->status) }}
                            @endif
                        </span>
                    </div>

                    <!-- CRUD Action Buttons Strip -->
                    <div class="flex items-center justify-between gap-1 pt-1 border-t border-gray-50 dark:border-slate-800 text-[11px]">
                        <!-- READ Button: Buka Laporan -->
                        <button type="button" 
                                wire:click="viewTicket({{ $t->id }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>{{ __('Buka Laporan') }}</span>
                        </button>

                        @if ($isOwner)
                            @if ($canModify)
                                <!-- UPDATE & DELETE (Belum di-acc admin / Pending) -->
                                <div class="flex items-center gap-1">
                                    <button type="button" 
                                            wire:click="openEditModal({{ $t->id }})" 
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition cursor-pointer"
                                            title="{{ __('Koreksi kesalahan / salah ketik pada laporan') }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>{{ __('Edit') }}</span>
                                    </button>

                                    <button type="button" 
                                            wire:click="deleteTicket({{ $t->id }})" 
                                            wire:confirm="{{ __('Apakah Anda yakin ingin membatalkan & menghapus laporan ini?') }}"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                                            title="{{ __('Hapus laporan') }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>{{ __('Hapus') }}</span>
                                    </button>
                                </div>
                            @else
                                <!-- LOCKED (Sudah di-acc oleh admin / In Progress / Resolved) -->
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 dark:text-slate-500 font-medium" title="{{ __('Laporan ini telah di-acc oleh admin dan sedang/telah diproses, sehingga tidak dapat diubah lagi.') }}">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span>{{ __('Di-ACC Admin') }}</span>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 px-4 rounded-xl border-2 border-dashed border-gray-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ __('Belum Ada Tiket yang Dibuat') }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">{{ __('Antrean tiket antar divisi saat ini masih bersih. Silakan buat tiket pertama Anda melalui formulir di atas untuk memulai koordinasi antar divisi.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- READ MODAL: MODAL DETAIL LENGKAP LAPORAN (Bisa melihat laporan baru & selesai) -->
    <!-- ========================================================================= -->
    @if ($isDetailModalOpen && $viewingTicket)
        @php
            $isOwnerViewing = $this->isOwner($viewingTicket);
            $canModifyViewing = $this->canModifyTicket($viewingTicket);
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
             x-data
             x-init="$el.focus()"
             @keydown.escape.window="$wire.closeDetailModal()">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs transition-opacity"
                 wire:click="closeDetailModal"></div>

            <!-- Modal Content Card -->
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden z-10 animate-fade-in text-slate-800 dark:text-slate-100">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center font-mono font-bold text-xs shadow-xs">
                            #{{ $viewingTicket->id }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ $viewingTicket->title }}</span>
                            </h3>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                {{ __('Dibuat oleh:') }} <strong class="text-slate-600 dark:text-slate-300">{{ $viewingTicket->sender->name ?? $viewingTicket->user->name ?? __('Karyawan') }}</strong>
                                &bull; {{ $viewingTicket->created_at ? $viewingTicket->created_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6 space-y-5 text-xs">
                    
                    <!-- Status Banner Indicator -->
                    <div class="p-3.5 rounded-xl border flex items-center justify-between gap-3 {{ $viewingTicket->status === 'Pending' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200' : ($viewingTicket->status === 'Resolved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200') }}">
                        <div class="flex items-center gap-2.5">
                            @if ($viewingTicket->status === 'Pending')
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <div>
                                    <p class="font-bold text-xs">{{ __('Status: Menunggu ACC / Verifikasi Admin') }}</p>
                                    <p class="text-[11px] opacity-80">{{ __('Laporan belum di-acc oleh admin. Anda masih dapat mengedit atau menghapus laporan ini.') }}</p>
                                </div>
                            @elseif ($viewingTicket->status === 'Resolved')
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <div>
                                    <p class="font-bold text-xs">{{ __('Status: Telah Selesai Ditangani (Resolved)') }}</p>
                                    <p class="text-[11px] opacity-80">{{ __('Kendala pada laporan ini telah berhasil diselesaikan oleh tim teknis.') }}</p>
                                </div>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                <div>
                                    <p class="font-bold text-xs">{{ __('Status: Telah di-ACC & Sedang Dikerjakan') }} ({{ $viewingTicket->status }})</p>
                                    <p class="text-[11px] opacity-80">{{ __('Laporan telah di-acc oleh admin dan dalam proses penanganan. Data terkunci dari perubahan.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Information Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Departemen Tujuan') }}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block truncate">{{ $viewingTicket->targetDepartment->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Kategori') }}</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5 block">{{ $viewingTicket->category }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Prioritas') }}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $viewingTicket->priority }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Status') }}</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400 mt-0.5 block">{{ $viewingTicket->status }}</span>
                        </div>
                    </div>

                    <!-- Problem Details / Description -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">{{ __('Deskripsi / Rincian Masalah') }}</h4>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 leading-relaxed whitespace-pre-line shadow-2xs">
                            {{ $viewingTicket->description }}
                        </div>
                    </div>

                    <!-- Photo Evidence if available -->
                    @if ($viewingTicket->photo_url)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">{{ __('Bukti Lampiran Foto') }}</h4>
                            <div class="p-2 rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 inline-block">
                                <a href="{{ $viewingTicket->photo_url }}" target="_blank" rel="noopener noreferrer" title="{{ __('Klik untuk melihat ukuran penuh') }}">
                                    <img src="{{ $viewingTicket->photo_url }}" 
                                         alt="{{ __('Bukti Foto') }}" 
                                         class="max-h-56 rounded-lg object-contain border border-gray-300 dark:border-slate-600 hover:opacity-95 transition"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'320\' height=\'160\' viewBox=\'0 0 320 160\'><rect width=\'100%\' height=\'100%\' fill=\'%23f1f5f9\'/><text x=\'50%\' y=\'50%\' font-family=\'system-ui, sans-serif\' font-size=\'12\' fill=\'%2364748b\' text-anchor=\'middle\'>Foto Lampiran Diarsipkan</text></svg>';">
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Thread: Riwayat Tanggapan & Jawaban Admin -->
                    <div class="pt-3 border-t border-gray-100 dark:border-slate-800">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3 flex items-center justify-between">
                            <span>{{ __('Jawaban / Tindak Lanjut Petugas:') }} ({{ $viewingTicket->messages->count() }})</span>
                        </h4>

                        @if ($viewingTicket->messages->isEmpty())
                            <div class="p-4 rounded-xl border border-dashed border-gray-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-center text-slate-400 dark:text-slate-500 text-xs">
                                <p>{{ __('Belum ada catatan atau jawaban dari petugas.') }}</p>
                            </div>
                        @else
                            <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                                @foreach ($viewingTicket->messages as $msg)
                                    <div class="p-3 rounded-xl border bg-blue-50/60 dark:bg-blue-950/40 border-blue-200 dark:border-blue-900/60">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <span class="font-bold text-slate-900 dark:text-white text-xs">
                                                {{ $msg->user?->name ?? __('Petugas IT') }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500">
                                                {{ $msg->created_at ? $msg->created_at->diffForHumans() : '-' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed">
                                            {{ $msg->message }}
                                        </p>
                                        @if ($msg->photo_url)
                                            <div class="mt-2.5">
                                                <a href="{{ $msg->photo_url }}" target="_blank" rel="noopener noreferrer" class="inline-block group" title="{{ __('Klik untuk melihat bukti foto ukuran penuh') }}">
                                                    <img src="{{ $msg->photo_url }}" alt="Bukti Foto Petugas" class="max-h-48 rounded-lg border border-blue-200 dark:border-blue-800 shadow-xs object-cover hover:opacity-95 transition">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Modal Footer with Update & Delete Controls -->
                <div class="px-6 py-3.5 border-t border-gray-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 bg-slate-50/70 dark:bg-slate-800/40 shrink-0">
                    <div class="flex items-center gap-2">
                        @if ($isOwnerViewing)
                            @if ($canModifyViewing)
                                <!-- UPDATE BUTTON (Jika Belum di-acc Admin) -->
                                <button type="button" 
                                        wire:click="openEditModal({{ $viewingTicket->id }})" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/60 dark:hover:bg-amber-900 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>{{ __('Edit Laporan (Koreksi)') }}</span>
                                </button>

                                <!-- DELETE BUTTON (Jika Belum di-acc Admin) -->
                                <button type="button" 
                                        wire:click="deleteTicket({{ $viewingTicket->id }})" 
                                        wire:confirm="{{ __('Apakah Anda yakin ingin membatalkan & menghapus laporan ini?') }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>{{ __('Hapus Laporan') }}</span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-200 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span>{{ __('Terkunci: Sudah di-ACC Admin') }}</span>
                                </span>
                            @endif
                        @endif
                    </div>

                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                        {{ __('Tutup') }}
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- UPDATE MODAL: MODAL EDIT LAPORAN (Khusus posisi Belum di-acc oleh Admin) -->
    <!-- ========================================================================= -->
    @if ($isEditModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
             x-data
             x-init="$el.focus()"
             @keydown.escape.window="$wire.closeEditModal()">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs transition-opacity"
                 wire:click="closeEditModal"></div>

            <!-- Modal Content Card -->
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden z-10 animate-fade-in text-slate-800 dark:text-slate-100">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ __('Koreksi / Perbarui Laporan') }} #{{ $editingTicketId }}</span>
                            </h3>
                            <p class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">{{ __('Posisi: Belum di-ACC oleh Admin &bull; Perbaiki salah ketik atau data kendala') }}</p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeEditModal" 
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body (Form) -->
                <form wire:submit.prevent="updateTicket" class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Title -->
                    <div>
                        <label for="modal_edit_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Judul Laporan Kendala') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="modal_edit_title" wire:model="editTitle"
                               class="w-full px-3.5 py-2 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                        @error('editTitle') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Target Department & Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_edit_target_dept" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ __('Departemen Tujuan') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="modal_edit_target_dept" wire:model="editTargetDepartmentId"
                                    class="w-full px-3 py-2 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                                @foreach($targetDepartments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('editTargetDepartmentId') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="modal_edit_category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ __('Kategori Kendala') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="modal_edit_category" wire:model="editCategory"
                                    class="w-full px-3 py-2 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                                @foreach($this->editAvailableCategories as $cat)
                                    <option value="{{ $cat }}">{{ __($cat) }}</option>
                                @endforeach
                            </select>
                            @error('editCategory') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Priority Level -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Tingkat Prioritas') }}</label>
                        @php
                            $priorities = [
                                'Low' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-700 dark:hover:text-emerald-300', 'active' => 'bg-emerald-600 text-white border-emerald-600 shadow-sm'],
                                'Medium' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/50 hover:text-blue-700 dark:hover:text-blue-300', 'active' => 'bg-blue-600 text-white border-blue-600 shadow-sm'],
                                'High' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-amber-950/50 hover:text-amber-700 dark:hover:text-amber-300', 'active' => 'bg-amber-500 text-white border-amber-500 shadow-sm'],
                                'Critical' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-950/50 hover:text-rose-700 dark:hover:text-rose-300', 'active' => 'bg-rose-600 text-white border-rose-600 shadow-sm'],
                            ];
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach ($priorities as $level => $styles)
                                <button type="button" wire:click="setEditPriority('{{ $level }}')" 
                                        class="py-2 px-2 text-xs font-bold rounded-lg border text-center transition cursor-pointer {{ $editPriority === $level ? $styles['active'] : $styles['base'] }}">
                                    {{ __($level) }}
                                </button>
                            @endforeach
                        </div>
                        @error('editPriority') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="modal_edit_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Rincian Deskripsi Masalah') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="modal_edit_description" wire:model="editDescription" rows="4"
                                  class="w-full px-3.5 py-2.5 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs leading-relaxed"></textarea>
                        @error('editDescription') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Photo Attachment -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Bukti Foto Kendala (Opsional)') }}
                        </label>
                        
                        <!-- Existing Photo Preview -->
                        @if ($existingPhotoUrl && ! $removeExistingPhoto)
                            <div class="mb-3 p-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $existingPhotoUrl }}" alt="Foto Tiket" class="w-12 h-12 rounded-lg object-cover border border-gray-300 dark:border-slate-600 shadow-xs">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ __('Foto Tersimpan') }}</p>
                                        <span class="text-[11px] text-slate-400">{{ __('Foto akan tetap digunakan kecuali Anda menghapusnya') }}</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="markRemoveExistingPhoto" class="px-2.5 py-1 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg font-semibold transition border border-rose-200 dark:border-rose-800 cursor-pointer">
                                    {{ __('Hapus Foto') }}
                                </button>
                            </div>
                        @endif

                        <!-- New Photo Upload Preview -->
                        @if ($editPhoto)
                            <div class="relative rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-950/30 p-3 mb-2 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $editPhoto->temporaryUrl() }}" alt="Preview Foto Baru" class="w-12 h-12 rounded-lg object-cover border border-blue-300 dark:border-blue-700 shadow-xs">
                                    <div>
                                        <p class="text-xs font-bold text-blue-900 dark:text-blue-100">{{ __('Foto Baru Terpilih') }}</p>
                                        <span class="text-[11px] text-blue-700 dark:text-blue-300">{{ number_format($editPhoto->getSize() / 1024, 1) }} KB</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="$set('editPhoto', null)" class="text-xs text-rose-600 dark:text-rose-400 hover:underline font-semibold cursor-pointer">
                                    {{ __('Batal Ganti') }}
                                </button>
                            </div>
                        @else
                            <input type="file" wire:model="editPhoto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-slate-300 cursor-pointer">
                        @endif

                        <div wire:loading wire:target="editPhoto" class="text-xs text-blue-600 dark:text-blue-400 mt-1 flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>{{ __('Sedang mengunggah foto...') }}</span>
                        </div>
                        @error('editPhoto') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Actions Footer -->
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeEditModal"
                                class="px-4 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-5 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-md flex items-center gap-2 cursor-pointer disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateTicket" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>{{ __('Simpan Perubahan') }}</span>
                            </span>
                            <span wire:loading wire:target="updateTicket" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>{{ __('Menyimpan...') }}</span>
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

</div>
