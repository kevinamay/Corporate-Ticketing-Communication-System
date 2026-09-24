<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-md p-5 sm:p-6 transition-colors">
    <!-- Header Strip with Icon & Explanation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-5 border-b border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center shadow-xs">
                <!-- Globe / Transparency SVG -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                        {{ __('Tiket Global (Transparansi Seluruh Departemen)') }}
                    </h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        {{ $tickets->total() }} {{ __('Tiket') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ __('Semua karyawan dapat memantau tiket antar-divisi, namun tindakan pemrosesan hanya dapat dilakukan oleh departemen yang dituju.') }}
                </p>
            </div>
        </div>

        <!-- Current User Department Indicator Badge -->
        <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 text-xs">
            <span class="text-slate-500 dark:text-slate-400 font-medium">{{ __('Departemen Anda:') }}</span>
            <span class="font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ $currentUser?->department?->name ?? __('Belum login') }}
            </span>
        </div>
    </div>

    <!-- Notification Banners -->
    @if (session()->has('handle_success'))
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between animate-fade-in shadow-xs">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('handle_success') }}</span>
            </span>
            <a href="#chat-pane" class="underline text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 font-bold ml-2">
                {{ __('Buka di Live Chat Desk &rarr;') }}
            </a>
        </div>
    @endif

    @if (session()->has('unauthorized_error'))
        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2 animate-fade-in shadow-xs">
            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('unauthorized_error') }}</span>
        </div>
    @endif

    <!-- Search & Filter Controls Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 mb-5">
        <!-- Search Input -->
        <div class="lg:col-span-5 relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="{{ __('Cari tiket, judul, kategori, atau nama staf...') }}"
                   class="w-full pl-9 pr-3.5 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Target Department Filter -->
        <div class="lg:col-span-3">
            <select wire:model.live="departmentFilter" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-2xs">
                <option value="all">{{ __('Semua Departemen Tujuan') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="lg:col-span-2">
            <select wire:model.live="statusFilter" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-2xs">
                <option value="all">{{ __('Semua Status') }}</option>
                <option value="Pending">{{ __('Pending') }}</option>
                <option value="Open">{{ __('Open') }}</option>
                <option value="In Progress">{{ __('In Progress') }}</option>
                <option value="Resolved">{{ __('Resolved') }}</option>
            </select>
        </div>

        <!-- Priority Filter -->
        <div class="lg:col-span-2">
            <select wire:model.live="priorityFilter" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-2xs">
                <option value="all">{{ __('Semua Prioritas') }}</option>
                <option value="Critical">{{ __('Critical') }}</option>
                <option value="High">{{ __('High') }}</option>
                <option value="Medium">{{ __('Medium') }}</option>
                <option value="Low">{{ __('Low') }}</option>
            </select>
        </div>
    </div>

    <!-- Data Table of Global Tickets -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                <tr>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Ticket ID') }}</th>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Judul & Pelapor') }}</th>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Departemen Tujuan') }}</th>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Kategori') }}</th>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Prioritas') }}</th>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Status') }}</th>
                    <th scope="col" class="py-3.5 px-3.5 text-right">{{ __('Aksi / Penanganan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                @forelse ($tickets as $ticket)
                    @php
                        $priorityStyle = match($ticket->priority) {
                            'Critical' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                            'High' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                            'Medium' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                            default => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                        };
                        $statusStyle = match($ticket->status) {
                            'Resolved' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                            'In Progress' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                            'Open' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-gray-200 dark:border-slate-700',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <!-- ID -->
                        <td class="py-3 px-3.5 font-mono font-bold text-slate-500 dark:text-slate-400">
                            #{{ $ticket->id }}
                        </td>

                        <!-- Title & Sender -->
                        <td class="py-3 px-3.5">
                            <button type="button" 
                                    wire:click="viewTicketDetail({{ $ticket->id }})" 
                                    class="text-left font-bold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer max-w-[220px] truncate block" 
                                    title="{{ $ticket->title }}">
                                {{ $ticket->title }}
                            </button>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1 mt-0.5">
                                <span>{{ __('Oleh:') }}</span>
                                <strong class="text-slate-600 dark:text-slate-400 font-semibold">{{ $ticket->sender->name ?? $ticket->user->name ?? 'Anonim' }}</strong>
                                <span>&bull;</span>
                                <span>{{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}</span>
                            </span>
                        </td>

                        <!-- Target Department -->
                        <td class="py-3 px-3.5">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-800 dark:text-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                {{ $ticket->targetDepartment->name ?? '-' }}
                            </span>
                        </td>

                        <!-- Category -->
                        <td class="py-3 px-3.5">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium border border-gray-200 dark:border-slate-700 text-[11px]">
                                {{ $ticket->category }}
                            </span>
                        </td>

                        <!-- Priority -->
                        <td class="py-3 px-3.5">
                            <span class="px-2 py-0.5 rounded border font-bold text-[10px] {{ $priorityStyle }}">
                                {{ __($ticket->priority) }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-3.5">
                            <span class="px-2 py-0.5 rounded border font-semibold text-[10px] {{ $statusStyle }}">
                                {{ __($ticket->status) }}
                            </span>
                        </td>

                        <!-- ======================================================== -->
                        <!-- CONDITIONAL ACTION / AUTHORIZATION LOGIC (CRUCIAL) -->
                        <!-- ======================================================== -->
                        <td class="py-3 px-3.5 text-right whitespace-nowrap">
                            @if (auth()->check() && auth()->user()->department_id === $ticket->target_department_id)
                                <!-- If TRUE (Authorized): Show a "Handle Ticket" or "Reply" button (bg-blue-600) -->
                                <button type="button" 
                                        wire:click="handleTicket({{ $ticket->id }})" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150 cursor-pointer active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span>{{ __('Handle Ticket') }}</span>
                                </button>
                            @else
                                <!-- If FALSE (Unauthorized): Hide all action buttons. Instead, show a grey badge that says "View Only - Not Your Dept" (bg-gray-200 text-gray-600) -->
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-200 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                                    <svg class="w-3 h-3 text-gray-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span>{{ __('View Only - Not Your Dept') }}</span>
                                </span>
                            @endif

                            <!-- Quick View Detail Eye Button -->
                            <button type="button" 
                                    wire:click="viewTicketDetail({{ $ticket->id }})" 
                                    title="{{ __('Lihat Detail') }}"
                                    class="ml-1.5 p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400 dark:text-slate-500">
                            <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-xs font-semibold">{{ __('Tidak ada tiket yang sesuai dengan kriteria pencarian.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>

    <!-- ========================================== -->
    <!-- TICKET DETAIL MODAL (With Authorization Check) -->
    <!-- ========================================== -->
    @if ($isDetailModalOpen && $viewingTicket)
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
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-mono font-bold text-xs shadow-xs">
                            #{{ $viewingTicket->id }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ $viewingTicket->title }}</span>
                            </h3>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                {{ __('Diajukan oleh') }} <strong class="text-slate-600 dark:text-slate-300">{{ $viewingTicket->sender->name ?? $viewingTicket->user->name ?? 'Anonim' }}</strong>
                                &bull; {{ $viewingTicket->created_at ? $viewingTicket->created_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4 text-xs">
                    <!-- Ticket Meta Badges Grid -->
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

                    <!-- Detailed Description -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">{{ __('Deskripsi Kendala') }}</h4>
                        <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 leading-relaxed whitespace-pre-line shadow-2xs">
                            {{ $viewingTicket->description }}
                        </div>
                    </div>

                    <!-- Photo Evidence Attachment if any -->
                    @if ($viewingTicket->photo_url)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">{{ __('Bukti Lampiran Foto') }}</h4>
                            <div class="p-2 rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 inline-block">
                                <a href="{{ $viewingTicket->photo_url }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $viewingTicket->photo_url }}" alt="Bukti Foto Kendala" class="max-h-64 rounded-lg object-contain border border-gray-300 dark:border-slate-600 hover:opacity-95 transition">
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer with Authorization Check -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40 shrink-0">
                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="px-4 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                        {{ __('Tutup') }}
                    </button>

                    <!-- Exact Authorization Conditional in Detail Modal -->
                    <div>
                        @if (auth()->check() && auth()->user()->department_id === $viewingTicket->target_department_id)
                            <!-- Authorized Action -->
                            <button type="button" 
                                    wire:click="handleTicket({{ $viewingTicket->id }})" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>{{ __('Handle Ticket') }}</span>
                            </button>
                        @else
                            <!-- Unauthorized Badge -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-200 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span>{{ __('View Only - Not Your Dept') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
