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
                {{ __($currentUser?->department?->name ?? '') ?: __('Belum login') }}
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
            <a href="#queue" class="underline text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 font-bold ml-2">
                {{ __('Buka di Antrean Tiket &rarr;') }}
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
        <div class="lg:col-span-4">
            <select wire:model.live="departmentFilter" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-2xs">
                <option value="all">{{ __('Semua Departemen Tujuan') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ __($dept->name) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="lg:col-span-3">
            <select wire:model.live="statusFilter" 
                    class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-2xs">
                <option value="all">{{ __('Semua Status') }}</option>
                <option value="Pending">{{ __('Pending') }}</option>
                <option value="Open">{{ __('Open') }}</option>
                <option value="In Progress">{{ __('In Progress') }}</option>
                <option value="Resolved">{{ __('Resolved') }}</option>
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
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Waktu Masuk') }}</th>
                    <th scope="col" class="py-3.5 px-3.5">{{ __('Status') }}</th>
                    <th scope="col" class="py-3.5 px-3.5 text-right">{{ __('Aksi / Penanganan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                @forelse ($tickets as $ticket)
                    @php
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
                                {{ __($ticket->targetDepartment->name ?? '-') }}
                            </span>
                        </td>

                        <!-- Category -->
                        <td class="py-3 px-3.5">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium border border-gray-200 dark:border-slate-700 text-[11px]">
                                {{ $ticket->category }}
                            </span>
                        </td>

                        <!-- Timestamp Masuk (Urutan Pengerjaan Admin) -->
                        <td class="py-3 px-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1.5 font-mono font-bold text-slate-900 dark:text-slate-100 text-xs">
                                <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i:s') : '-' }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block pl-5 mt-0.5 font-medium">
                                {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '' }}
                            </span>
                        </td>

                        @php
                            $canHandle = $this->isAuthorizedForTicket($currentUser, $ticket);
                        @endphp

                        <!-- Status -->
                        <td class="py-3 px-3.5">
                            @if ($canHandle)
                                <div class="relative inline-flex items-center">
                                    <select wire:change="updateTicketStatus({{ $ticket->id }}, $event.target.value)" 
                                            title="{{ __('Perbarui status laporan (Khusus Admin/Petugas)') }}"
                                            class="px-2 py-1 rounded-lg border font-bold text-[10px] cursor-pointer outline-none transition focus:ring-2 focus:ring-blue-500 shadow-2xs {{ $statusStyle }}">
                                        <option value="Pending" {{ $ticket->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </div>
                            @else
                                <span class="px-2 py-0.5 rounded border font-semibold text-[10px] {{ $statusStyle }}">
                                    {{ __($ticket->status) }}
                                </span>
                            @endif
                        </td>

                        <!-- ======================================================== -->
                        <!-- CONDITIONAL ACTION / AUTHORIZATION LOGIC -->
                        <!-- ======================================================== -->
                        <td class="py-3 px-3.5 text-right whitespace-nowrap">
                            @if ($canHandle)
                                <button type="button" 
                                        wire:click="handleTicket({{ $ticket->id }})" 
                                        title="Handle Ticket"
                                        aria-label="Handle Ticket"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150 cursor-pointer active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span>{{ __('Jawab & Proses Tiket') }}</span>
                                </button>
                            @else
                                <span title="View Only - Not Your Dept" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-200 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                                    <svg class="w-3 h-3 text-gray-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span>{{ __('View Only') }}</span>
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
    <!-- TICKET DETAIL & ANSWERING MODAL -->
    <!-- ========================================== -->
    @if ($isDetailModalOpen && $viewingTicket)
        @php
            $isAuthorized = $this->isAuthorizedForTicket(auth()->user(), $viewingTicket);
            $isSender = auth()->check() && (int) auth()->id() === (int) $viewingTicket->sender_id;
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
                <div class="flex-1 overflow-y-auto p-6 space-y-5 text-xs">
                    <!-- Flash Message inside Modal -->
                    @if (session()->has('reply_success'))
                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>{{ session('reply_success') }}</span>
                        </div>
                    @endif

                    <!-- Ticket Meta Badges Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Departemen Tujuan') }}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block truncate">{{ __($viewingTicket->targetDepartment->name ?? '-') }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Kategori') }}</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5 block">{{ $viewingTicket->category }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Waktu Masuk') }}</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-slate-100 mt-0.5 block text-xs">
                                {{ $viewingTicket->created_at ? $viewingTicket->created_at->format('d/m/Y H:i:s') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">{{ __('Status') }}</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400 mt-0.5 block">{{ $viewingTicket->status }}</span>
                        </div>
                    </div>

                    <!-- Detailed Description -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">{{ __('Deskripsi Kendala') }}</h4>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 leading-relaxed shadow-2xs prose-ticket">
                            {!! $viewingTicket->formatted_description !!}
                        </div>
                    </div>

                    <!-- Photo Evidence Attachment if any -->
                    @if ($viewingTicket->photo_url)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">{{ __('Bukti Lampiran Foto') }}</h4>
                            <div class="p-2 rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 inline-block">
                                <a href="{{ $viewingTicket->photo_url }}" target="_blank" rel="noopener noreferrer" title="{{ __('Klik untuk melihat foto ukuran penuh') }}">
                                    <img src="{{ $viewingTicket->photo_url }}" 
                                         alt="{{ __('Bukti Foto Kendala') }}" 
                                         class="max-h-56 rounded-lg object-contain border border-gray-300 dark:border-slate-600 hover:opacity-95 transition"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'320\' height=\'160\' viewBox=\'0 0 320 160\'><rect width=\'100%\' height=\'100%\' fill=\'%23f1f5f9\'/><text x=\'50%\' y=\'50%\' font-family=\'system-ui, sans-serif\' font-size=\'12\' fill=\'%2364748b\' text-anchor=\'middle\'>Foto Lampiran Diarsipkan</text></svg>';">
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- ============================================== -->
                    <!-- RIWAYAT JAWABAN & TANGGAPAN TIKET (THREAD) -->
                    <!-- ============================================== -->
                    <div class="pt-3 border-t border-gray-100 dark:border-slate-800">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3 flex items-center justify-between">
                            <span>{{ __('Riwayat Jawaban & Tanggapan Masuk') }} ({{ $viewingTicket->messages->count() }})</span>
                            <span class="text-[10px] text-slate-400 font-normal">{{ __('Pembaruan Langsung') }}</span>
                        </h4>

                        @if ($viewingTicket->messages->isEmpty())
                            <div class="p-4 rounded-xl border border-dashed border-gray-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-center text-slate-400 dark:text-slate-500 text-xs">
                                <p>{{ __('Belum ada jawaban atau tanggapan untuk tiket ini.') }}</p>
                                @if ($isAuthorized)
                                    <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-1 font-semibold">{{ __('Tuliskan jawaban atau konfirmasi solusi pada formulir di bawah.') }}</p>
                                @endif
                            </div>
                        @else
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                                @foreach ($viewingTicket->messages as $msg)
                                    @php
                                        $isMsgFromSender = (int) $msg->user_id === (int) $viewingTicket->sender_id;
                                    @endphp
                                    <div class="p-3 rounded-xl border {{ $isMsgFromSender ? 'bg-slate-50 dark:bg-slate-800/50 border-gray-200 dark:border-slate-700' : 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200 dark:border-blue-900/60' }}">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-slate-900 dark:text-white text-xs">
                                                    {{ $msg->user?->name ?? 'Pengguna' }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $isMsgFromSender ? 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' : 'bg-blue-600 text-white' }}">
                                                    {{ $isMsgFromSender ? __('Pelapor') : ($msg->user?->role === 'admin' ? __('Admin') : __('Petugas / Dept')) }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500">
                                                {{ $msg->created_at ? $msg->created_at->diffForHumans() : '-' }}
                                            </span>
                                        </div>
                                        <div class="prose prose-ticket max-w-none text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
                                            {!! $msg->formatted_message !!}
                                        </div>
                                        @if ($msg->photo_url)
                                            <div class="mt-2.5">
                                                <a href="{{ $msg->photo_url }}" target="_blank" rel="noopener noreferrer" class="inline-block group" title="{{ __('Klik untuk melihat bukti foto ukuran penuh') }}">
                                                    <img src="{{ $msg->photo_url }}" alt="Bukti Foto Tanggapan" class="max-h-48 rounded-lg border border-slate-300 dark:border-slate-700 shadow-xs object-cover hover:opacity-95 transition">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- ============================================== -->
                    <!-- FORM MENJAWAB TIKET (KHUSUS ADMIN / DEPT TERKAIT) -->
                    <!-- ============================================== -->
                    @if ($isAuthorized || $isSender)
                        <div class="pt-3 border-t border-gray-100 dark:border-slate-800">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                                {{ $isAuthorized ? __('Tulis Jawaban / Tanggapan Petugas:') : __('Kirim Balasan / Info Tambahan:') }}
                            </h4>

                            @if ($isAuthorized)
                                <!-- Quick Status Switcher for Authorized Admin/Agents -->
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ __('Perbarui Status:') }}</span>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" 
                                                wire:click="updateTicketStatus({{ $viewingTicket->id }}, 'In Progress')" 
                                                class="px-2.5 py-1 rounded-md text-[11px] font-semibold transition cursor-pointer {{ $viewingTicket->status === 'In Progress' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                                            {{ __('Sedang Dikerjakan (In Progress)') }}
                                        </button>
                                        <button type="button" 
                                                wire:click="updateTicketStatus({{ $viewingTicket->id }}, 'Resolved')" 
                                                class="px-2.5 py-1 rounded-md text-[11px] font-semibold transition cursor-pointer {{ $viewingTicket->status === 'Resolved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                                            {{ __('Selesai (Resolved)') }}
                                        </button>
                                        <button type="button" 
                                                wire:click="updateTicketStatus({{ $viewingTicket->id }}, 'Pending')" 
                                                class="px-2.5 py-1 rounded-md text-[11px] font-semibold transition cursor-pointer {{ $viewingTicket->status === 'Pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                                            {{ __('Pending') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-3" x-data="adminReplyEditor()" @click.away="closeMenus()" @keydown.escape.window="closeMenus()">
                                
                                <!-- Jira-Style Rich Editor Container for Admin Reply -->
                                <div class="rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition shadow-2xs overflow-hidden">
                                    
                                    <!-- Toolbar Header -->
                                    <div class="flex flex-wrap items-center gap-0.5 sm:gap-1 p-1.5 sm:p-2 border-b border-gray-200 dark:border-slate-700/80 bg-slate-50/90 dark:bg-slate-800/80 select-none text-xs">
                                        
                                        <!-- 1. AI Smart Solution Dropdown (✦) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('ai')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 transition cursor-pointer flex items-center gap-0.5 group"
                                                title="{{ __('AI Smart Solution Formatting') }}">
                                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 2L14.4 7.6L20 10L14.4 12.4L12 18L9.6 12.4L4 10L9.6 7.6L12 2Z" fill="url(#adminAiGradient)" />
                                                    <path d="M19 15L20.2 17.8L23 19L20.2 20.2L19 23L17.8 20.2L15 19L17.8 17.8L19 15Z" fill="url(#adminAiGradient)" />
                                                    <defs>
                                                        <linearGradient id="adminAiGradient" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#3b82f6" />
                                                            <stop offset="0.33" stop-color="#8b5cf6" />
                                                            <stop offset="0.66" stop-color="#ec4899" />
                                                            <stop offset="1" stop-color="#f59e0b" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                            </button>

                                            <div x-show="openMenu === 'ai'" x-cloak 
                                                class="absolute left-0 mt-1.5 w-64 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1.5 z-50 text-xs">
                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-gray-100 dark:border-slate-700/60 mb-1">
                                                    {{ __('AI Smart Formatting') }}
                                                </div>
                                                <button type="button" @click="improveAnswer('smart_polish')" class="w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-slate-700 flex items-start gap-2.5 text-slate-700 dark:text-slate-200 transition cursor-pointer">
                                                    <span class="text-blue-500 font-bold">✨</span>
                                                    <div>
                                                        <div class="font-semibold text-slate-900 dark:text-white">{{ __('Format Struktur Solusi & Penanganan') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Format teks menjadi laporan solusi: Tindakan, Analisis, & Status') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="improveAnswer('bullets')" class="w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-slate-700 flex items-start gap-2.5 text-slate-700 dark:text-slate-200 transition cursor-pointer">
                                                    <span class="text-amber-500 font-bold">📋</span>
                                                    <div>
                                                        <div class="font-semibold text-slate-900 dark:text-white">{{ __('Format Poin Instruksi') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Ubah baris menjadi daftar poin instruksi teratur') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="improveAnswer('clean')" class="w-full text-left px-3 py-2 hover:bg-rose-50 dark:hover:bg-slate-700 flex items-start gap-2.5 text-slate-700 dark:text-slate-200 transition border-t border-gray-100 dark:border-slate-700/60 cursor-pointer">
                                                    <span class="text-slate-400 font-bold">🧹</span>
                                                    <div>
                                                        <div class="font-semibold text-slate-900 dark:text-white">{{ __('Clean Formatting') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Kembalikan ke teks polos tanpa markdown') }}</div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 2. "Rapikan & Format Solusi" Wand Button -->
                                        <button type="button" @click="improveAnswer('smart_polish')" 
                                            class="px-2 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer flex items-center gap-1.5 text-xs font-medium border border-transparent hover:border-blue-200 dark:hover:border-blue-800"
                                            title="{{ __('Rapikan & Format Solusi') }}">
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                            <span>{{ __('Rapikan & Format Solusi') }}</span>
                                        </button>

                                        <!-- Divider -->
                                        <span class="h-4 w-px bg-gray-200 dark:bg-slate-700 mx-0.5 sm:mx-1"></span>

                                        <!-- 3. Heading (T ˅) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('heading')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5 font-bold text-xs"
                                                title="{{ __('Heading') }}">
                                                <span>T</span>
                                                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'heading'" x-cloak class="absolute left-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <button type="button" @click="applyHeading(1)" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Heading 1 (Main Title)') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">#</span>
                                                </button>
                                                <button type="button" @click="applyHeading(2)" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-xs text-slate-800 dark:text-slate-200 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Heading 2 (Section)') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">##</span>
                                                </button>
                                                <button type="button" @click="applyHeading(3)" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 font-medium text-xs text-slate-700 dark:text-slate-300 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Heading 3 (Point)') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">###</span>
                                                </button>
                                                <button type="button" @click="insertAtLineStart('')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs text-slate-600 dark:text-slate-400 border-t border-gray-100 dark:border-slate-700/60 mt-0.5 cursor-pointer">
                                                    {{ __('Normal Text') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 4. Typography (B ˅) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('bold')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5 font-bold text-xs"
                                                title="{{ __('Bold') }} (Ctrl+B)">
                                                <span>B</span>
                                                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'bold'" x-cloak class="absolute left-0 mt-1.5 w-40 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <button type="button" @click="wrapSelection('**', '**', 'teks tebal')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-slate-900 dark:text-white flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Bold') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">**B**</span>
                                                </button>
                                                <button type="button" @click="wrapSelection('*', '*', 'teks miring')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 italic text-slate-800 dark:text-slate-200 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Italic') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">*I*</span>
                                                </button>
                                                <button type="button" @click="wrapSelection('<u>', '</u>', 'teks garis bawah')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 underline text-slate-800 dark:text-slate-200 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Underline') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">&lt;u&gt;</span>
                                                </button>
                                                <button type="button" @click="wrapSelection('~~', '~~', 'teks dicoret')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 line-through text-slate-700 dark:text-slate-300 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Strikethrough') }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">~~S~~</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 5. Lists (≡ ˅) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('list')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5"
                                                title="{{ __('List') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
                                                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'list'" x-cloak class="absolute left-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <button type="button" @click="applyList('bullet')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span class="font-bold">•</span>
                                                    <span>{{ __('Bulleted List') }}</span>
                                                </button>
                                                <button type="button" @click="applyList('number')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span class="font-bold font-mono text-[11px]">1.</span>
                                                    <span>{{ __('Numbered List') }}</span>
                                                </button>
                                                <button type="button" @click="applyList('check')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span class="font-bold text-emerald-500">☑</span>
                                                    <span>{{ __('Checklist') }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 6. Badges & Color (A) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('color')" 
                                                class="p-1 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5"
                                                title="{{ __('Text Accent & Badges') }}">
                                                <span class="w-5 h-5 flex items-center justify-center border border-gray-300 dark:border-slate-600 rounded font-bold text-xs">A</span>
                                            </button>
                                            <div x-show="openMenu === 'color'" x-cloak class="absolute left-0 mt-1.5 w-48 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Text Accent & Badges') }}</div>
                                                <button type="button" @click="applyColor('SOLVED')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold cursor-pointer">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                                    <span>🟢 {{ __('Success / Resolved') }}</span>
                                                </button>
                                                <button type="button" @click="applyColor('IN PROGRESS')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-purple-600 dark:text-purple-400 font-semibold cursor-pointer">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                                    <span>🟣 {{ __('In Progress') }}</span>
                                                </button>
                                                <button type="button" @click="applyColor('INFO')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold cursor-pointer">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                                    <span>🔵 {{ __('Important Info') }}</span>
                                                </button>
                                                <button type="button" @click="applyColor('URGENT')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-rose-600 dark:text-rose-400 font-semibold cursor-pointer">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                                    <span>🔴 {{ __('Urgent / Critical') }}</span>
                                                </button>
                                                <button type="button" @click="applyColor('WARNING')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-amber-600 dark:text-amber-400 font-semibold cursor-pointer">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                                    <span>🟠 {{ __('Warning') }}</span>
                                                </button>
                                                <button type="button" @click="wrapSelection('<mark>', '</mark>', 'teks sorotan')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-slate-800 dark:text-slate-200 border-t border-gray-100 dark:border-slate-700/60 mt-0.5 cursor-pointer">
                                                    <span class="px-1 bg-yellow-200 dark:bg-yellow-800 text-[10px] rounded font-bold">ABC</span>
                                                    <span>{{ __('Highlight Text') }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 7. Image Upload Trigger (🖼) -->
                                        <button type="button" @click="triggerPhotoUpload()" 
                                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                                            title="{{ __('Attach Photo / Screenshot') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15l-5-5L5 21"/>
                                            </svg>
                                        </button>

                                        <!-- 8. Code (</>) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('code')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer font-mono font-bold text-xs"
                                                title="{{ __('Inline Code') }}">
                                                &lt;/&gt;
                                            </button>
                                            <div x-show="openMenu === 'code'" x-cloak class="absolute left-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <button type="button" @click="insertCode(false)" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Inline Code') }}</span>
                                                    <span class="font-mono text-slate-400">`kode`</span>
                                                </button>
                                                <button type="button" @click="insertCode(true)" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-between cursor-pointer">
                                                    <span>{{ __('Code Block / Log') }}</span>
                                                    <span class="font-mono text-slate-400">```log```</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 9. Emoji (☺) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('emoji')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer text-xs"
                                                title="{{ __('Insert Emoji') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'emoji'" x-cloak class="absolute left-0 mt-1.5 w-60 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl p-2 z-50">
                                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ __('Insert Emoji') }}</div>
                                                <div class="grid grid-cols-7 gap-1 text-base text-center">
                                                    <template x-for="emo in ['✅', '🛠️', '🔧', '💻', '🖥️', '🖨️', '🔌', '🌐', '📡', '⚙️', '📄', '📌', '📝', '🔑', '❄️', '👍', '⚠️', '🚨', '💡', '🕒', '📞']">
                                                        <button type="button" @click="insertText(emo)" class="w-7 h-7 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center cursor-pointer text-sm" x-text="emo"></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 10. Insert More (+) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('insert')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer font-bold text-xs"
                                                title="{{ __('Insert More') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'insert'" x-cloak class="absolute left-0 mt-1.5 w-48 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <button type="button" @click="insertAtLineStart('> ')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span class="font-serif italic font-bold">“</span>
                                                    <span>{{ __('Insert Quote') }}</span>
                                                </button>
                                                <button type="button" @click="insertText('\n---\n')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>―</span>
                                                    <span>{{ __('Insert Divider') }}</span>
                                                </button>
                                                <button type="button" @click="insertText('\n| Langkah | Detail Solusi | Status |\n| --- | --- | --- |\n| 1 | Pengecekan teknis | Selesai |\n')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>▦</span>
                                                    <span>{{ __('Insert Table') }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 11. Link (🔗) -->
                                        <button type="button" @click="insertLink()" 
                                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                                            title="{{ __('Insert Link') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        </button>

                                        <!-- Divider -->
                                        <span class="h-4 w-px bg-gray-200 dark:bg-slate-700 mx-0.5 sm:mx-1"></span>

                                        <!-- 12. Undo (↶) -->
                                        <button type="button" @click="undo()" :disabled="historyIndex <= 0" 
                                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                                            title="{{ __('Undo') }} (Ctrl+Z)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"/></svg>
                                        </button>

                                        <!-- 13. Redo (↷) -->
                                        <button type="button" @click="redo()" :disabled="historyIndex >= history.length - 1" 
                                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                                            title="{{ __('Redo') }} (Ctrl+Y)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"/></svg>
                                        </button>

                                        <!-- 14. Solution Templates (🕒 ˅) -->
                                        <div class="relative inline-block">
                                            <button type="button" @click.stop="toggleMenu('template')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                                                title="{{ __('Template Solusi Penanganan') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 7v5l3 2"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'template'" x-cloak class="absolute right-0 sm:left-0 mt-1.5 w-64 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1.5 z-50 text-xs">
                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Template Solusi Petugas') }}</div>
                                                <button type="button" @click="insertTemplate('pc_fix')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>🖥️</span>
                                                    <div>
                                                        <div class="font-semibold">{{ __('Solusi Perbaikan PC / Hardware') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Pembersihan, restart service, hardware siap') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="insertTemplate('network_fix')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>🌐</span>
                                                    <div>
                                                        <div class="font-semibold">{{ __('Solusi Jaringan & Wi-Fi') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Reset IP, flush DNS, kabel LAN terverifikasi') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="insertTemplate('printer_fix')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>🖨️</span>
                                                    <div>
                                                        <div class="font-semibold">{{ __('Solusi Printer & Scanner') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Paper jam diatasi, toner baru, cetak normal') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="insertTemplate('account_reset')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>🔑</span>
                                                    <div>
                                                        <div class="font-semibold">{{ __('Reset Akun & Akses') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Kata sandi sementara, instruksi aktivasi') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="insertTemplate('sparepart_wait')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>📦</span>
                                                    <div>
                                                        <div class="font-semibold">{{ __('Status Pengadaan Sparepart') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Menunggu suku cadang, estimasi kedatangan') }}</div>
                                                    </div>
                                                </button>
                                                <button type="button" @click="insertTemplate('resolved_confirm')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span>✅</span>
                                                    <div>
                                                        <div class="font-semibold">{{ __('Konfirmasi Penyelesaian Masalah') }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ __('Kendala tuntas diperbaiki, siap dicoba') }}</div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 15. More Options (... ˅) -->
                                        <div class="relative inline-block ml-auto">
                                            <button type="button" @click.stop="toggleMenu('more')" 
                                                class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                                                title="{{ __('More Options') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                            </button>
                                            <div x-show="openMenu === 'more'" x-cloak class="absolute right-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                                                <button type="button" @click="togglePreview()" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                                    <span x-text="previewMode ? '✏️' : '👁️'"></span>
                                                    <span x-text="previewMode ? '{{ __('Kembali ke Edit') }}' : '{{ __('Mode Preview') }}'"></span>
                                                </button>
                                                <button type="button" @click="clearContent()" class="w-full text-left px-3 py-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center gap-2 border-t border-gray-100 dark:border-slate-700/60 cursor-pointer">
                                                    <span>🗑️</span>
                                                    <span>{{ __('Hapus Teks Jawaban') }}</span>
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Editor Area (Textarea vs Live Preview) -->
                                    <div class="relative">
                                        <!-- Edit Mode: Textarea -->
                                        <div x-show="!previewMode">
                                            <textarea x-ref="textarea"
                                                wire:model="replyMessage"
                                                @input="onInput()"
                                                @keydown="onKeydown($event)"
                                                rows="4"
                                                placeholder="{{ $isAuthorized ? __('Tuliskan jawaban, arahan teknis, atau solusi penanganan kendala untuk pelapor...') : __('Kirim pesan tanggapan atau informasi tambahan untuk petugas...') }}" 
                                                class="w-full text-xs p-3.5 bg-transparent border-0 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-0 resize-y leading-relaxed font-sans"></textarea>
                                        </div>

                                        <!-- Preview Mode -->
                                        <div x-show="previewMode" x-cloak 
                                            class="p-3.5 min-h-[110px] max-h-80 overflow-y-auto bg-slate-50/50 dark:bg-slate-900/40 border-t border-gray-100 dark:border-slate-800 text-xs">
                                            <div class="flex items-center justify-between pb-1.5 mb-2 border-b border-gray-200 dark:border-slate-700/60 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                                <span>{{ __('Preview Hasil Jawaban (Markdown):') }}</span>
                                                <button type="button" @click="togglePreview()" class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer lowercase first-letter:uppercase">
                                                    {{ __('Kembali ke Edit') }}
                                                </button>
                                            </div>
                                            <div class="prose prose-ticket max-w-none text-slate-800 dark:text-slate-100" x-html="renderMarkdown($refs.textarea ? $refs.textarea.value : '')"></div>
                                        </div>
                                    </div>

                                    <!-- Editor Status Bar Footer -->
                                    <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900/60 border-t border-gray-200 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-400 select-none">
                                        <div class="flex items-center gap-3">
                                            <span :class="charCount >= 2 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-400'">
                                                <span x-text="charCount">0</span> {{ __('characters') }}
                                                <template x-if="charCount >= 2">
                                                    <span class="ml-0.5">✓</span>
                                                </template>
                                            </span>
                                            <span>•</span>
                                            <span><span x-text="wordCount">0</span> {{ __('words') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="inline-flex rounded-md bg-slate-200/80 dark:bg-slate-700 p-0.5 text-[10px] font-bold">
                                                <button type="button" @click="if (previewMode) togglePreview()" 
                                                    class="px-2 py-0.5 rounded transition cursor-pointer" 
                                                    :class="!previewMode ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-2xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">
                                                    {{ __('Mode Edit') }}
                                                </button>
                                                <button type="button" @click="if (!previewMode) togglePreview()" 
                                                    class="px-2 py-0.5 rounded transition cursor-pointer" 
                                                    :class="previewMode ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-2xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">
                                                    {{ __('Mode Preview') }}
                                                </button>
                                            </div>
                                            <div class="hidden sm:flex items-center gap-1.5 text-[10px] text-slate-400 ml-2">
                                                <span>Ctrl+B: {{ __('Bold') }}</span>
                                                <span>•</span>
                                                <span>Ctrl+Z: {{ __('Undo') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @error('replyMessage') 
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 font-medium">{{ $message }}</p> 
                                @enderror

                                <!-- Sertakan Bukti Gambar Penanganan (Opsional) -->
                                <div class="p-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ __('Bukti Gambar / Foto (Opsional)') }}</span>
                                        </label>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ __('JPG, PNG, WebP &bull; Maks. 10MB') }}</span>
                                    </div>

                                    @if ($replyPhoto)
                                        <!-- Preview Foto Terpilih -->
                                        <div class="flex items-center justify-between p-2 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50/70 dark:bg-blue-950/40">
                                            <div class="flex items-center gap-2.5">
                                                <img src="{{ $replyPhoto->temporaryUrl() }}" alt="Preview Bukti" class="w-10 h-10 rounded-md object-cover border border-blue-300 dark:border-blue-700 shadow-2xs">
                                                <div>
                                                    <p class="text-xs font-bold text-blue-900 dark:text-blue-100">{{ __('Gambar Terpilih') }}</p>
                                                    <span class="text-[10px] text-blue-700 dark:text-blue-300">{{ number_format($replyPhoto->getSize() / 1024, 1) }} KB</span>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    wire:click="removeReplyPhoto" 
                                                    class="px-2.5 py-1 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-100/60 dark:hover:bg-rose-950/50 rounded-lg transition border border-rose-200 dark:border-rose-800 cursor-pointer">
                                                {{ __('Batal / Hapus') }}
                                            </button>
                                        </div>
                                    @else
                                        <!-- Upload Input Button -->
                                        <div class="flex items-center gap-2">
                                            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 cursor-pointer transition shadow-2xs">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <span>{{ __('Pilih Bukti Gambar') }}</span>
                                                <input id="admin_reply_photo" type="file" wire:model="replyPhoto" accept="image/*" class="hidden">
                                            </label>
                                            <span class="text-[11px] text-slate-400 dark:text-slate-500">
                                                {{ __('Foto alat, screenshot perbaikan, atau bukti dokumen pendukung.') }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Uploading Progress Indicator -->
                                    <div wire:loading wire:target="replyPhoto" class="text-xs text-blue-600 dark:text-blue-400 mt-2 flex items-center gap-1.5">
                                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                        <span>{{ __('Sedang mengunggah bukti gambar...') }}</span>
                                    </div>

                                    @error('replyPhoto') 
                                        <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-medium">{{ $message }}</p> 
                                    @enderror
                                </div>

                                <div class="flex items-center justify-end pt-1">
                                    <button type="button" 
                                            wire:click="sendTicketReply({{ $viewingTicket->id }})" 
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md transition cursor-pointer active:scale-95 disabled:opacity-50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="sendTicketReply">{{ __('Kirim Tanggapan / Jawaban') }}</span>
                                        <span wire:loading wire:target="sendTicketReply">{{ __('Mengirim...') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Anda melihat tiket ini dalam mode transparan (bukan departemen tujuan Anda).') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40 shrink-0">
                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="px-4 py-2 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                        {{ __('Tutup') }}
                    </button>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        {{ __('ID Tiket:') }} #{{ $viewingTicket->id }} &bull; {{ $viewingTicket->category }}
                    </span>
                </div>

            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminReplyEditor', () => adminReplyEditorImpl());
    });

    if (window.Alpine) {
        Alpine.data('adminReplyEditor', () => adminReplyEditorImpl());
    }

    function adminReplyEditor() {
        return adminReplyEditorImpl();
    }

    function adminReplyEditorImpl() {
        return {
            previewMode: false,
            openMenu: null,
            history: [],
            historyIndex: -1,
            maxHistory: 30,
            charCount: 0,
            wordCount: 0,

            init() {
                this.$nextTick(() => {
                    const el = this.$refs.textarea;
                    if (el) {
                        const initial = el.value || '';
                        this.history = [initial];
                        this.historyIndex = 0;
                        this.updateCounts(initial);
                    }
                });

                this.$watch('$wire.replyMessage', (val) => {
                    if ((val === '' || val === null || val === undefined) && this.$refs.textarea && this.$refs.textarea.value !== '') {
                        this.$refs.textarea.value = '';
                        this.updateCounts('');
                        this.history = [''];
                        this.historyIndex = 0;
                    }
                });
            },

            toggleMenu(name) {
                this.openMenu = this.openMenu === name ? null : name;
            },

            closeMenus() {
                this.openMenu = null;
            },

            togglePreview() {
                this.previewMode = !this.previewMode;
                this.closeMenus();
            },

            updateCounts(text) {
                this.charCount = text ? text.length : 0;
                const words = text ? text.trim().split(/\s+/).filter(Boolean) : [];
                this.wordCount = words.length;
            },

            onInput() {
                const val = this.$refs.textarea.value;
                this.updateCounts(val);
                this.pushHistory(val);
            },

            pushHistory(val) {
                if (this.history[this.historyIndex] === val) return;
                if (this.historyIndex < this.history.length - 1) {
                    this.history = this.history.slice(0, this.historyIndex + 1);
                }
                this.history.push(val);
                if (this.history.length > this.maxHistory) {
                    this.history.shift();
                } else {
                    this.historyIndex++;
                }
            },

            undo() {
                if (this.historyIndex > 0) {
                    this.historyIndex--;
                    this.setText(this.history[this.historyIndex]);
                }
            },

            redo() {
                if (this.historyIndex < this.history.length - 1) {
                    this.historyIndex++;
                    this.setText(this.history[this.historyIndex]);
                }
            },

            setText(val) {
                const el = this.$refs.textarea;
                if (!el) return;
                el.value = val;
                this.updateCounts(val);
                el.dispatchEvent(new Event('input', { bubbles: true }));
                if (this.$wire) {
                    this.$wire.set('replyMessage', val);
                }
            },

            wrapSelection(prefix, suffix, defaultPlaceholder = '') {
                const el = this.$refs.textarea;
                if (!el) return;
                el.focus();
                const start = el.selectionStart;
                const end = el.selectionEnd;
                const val = el.value;
                const selected = val.substring(start, end);
                const content = selected || defaultPlaceholder;
                const replacement = prefix + content + suffix;

                el.value = val.substring(0, start) + replacement + val.substring(end);
                el.selectionStart = start + prefix.length;
                el.selectionEnd = start + prefix.length + content.length;
                this.setText(el.value);
                this.closeMenus();
            },

            insertAtLineStart(prefix) {
                const el = this.$refs.textarea;
                if (!el) return;
                el.focus();
                const start = el.selectionStart;
                const val = el.value;
                const lineStart = val.lastIndexOf('\n', start - 1) + 1;
                
                el.value = val.substring(0, lineStart) + prefix + val.substring(lineStart);
                el.selectionStart = el.selectionEnd = start + prefix.length;
                this.setText(el.value);
                this.closeMenus();
            },

            insertText(text) {
                const el = this.$refs.textarea;
                if (!el) return;
                el.focus();
                const start = el.selectionStart;
                const end = el.selectionEnd;
                const val = el.value;
                
                el.value = val.substring(0, start) + text + val.substring(end);
                el.selectionStart = el.selectionEnd = start + text.length;
                this.setText(el.value);
                this.closeMenus();
            },

            applyHeading(level) {
                const prefix = '#'.repeat(level) + ' ';
                this.insertAtLineStart(prefix);
            },

            applyList(type) {
                if (type === 'bullet') this.insertAtLineStart('- ');
                else if (type === 'number') this.insertAtLineStart('1. ');
                else if (type === 'check') this.insertAtLineStart('- [ ] ');
            },

            applyColor(badge) {
                this.insertText('**[' + badge + ']** ');
            },

            insertCode(isBlock) {
                if (isBlock) {
                    this.wrapSelection("```\n", "\n```", "Tempelkan kode / log error di sini");
                } else {
                    this.wrapSelection('`', '`', 'kode_penanganan');
                }
            },

            insertLink() {
                const url = prompt('Masukkan URL tautan:', 'https://');
                if (url) {
                    this.wrapSelection('[', `](${url})`, 'Teks Tautan Solusi');
                }
            },

            triggerPhotoUpload() {
                const photoInput = document.getElementById('admin_reply_photo');
                if (photoInput) {
                    photoInput.click();
                }
                this.closeMenus();
            },

            getLocale() {
                const docLang = (document.documentElement.lang || '').substring(0, 2).toLowerCase();
                if (docLang === 'zh' || docLang === 'en' || docLang === 'id') {
                    return docLang;
                }
                return '{{ app()->getLocale() }}' || 'id';
            },

            getTemplates() {
                const lang = this.getLocale();
                if (lang === 'zh') {
                    return {
                        pc_fix: "### 🖥️ 电脑与硬件故障解决说明\n- **处理措施**: 清洁设备、重启系统相关服务并验证驱动程序。\n- **检测结果**: 各硬件组件运行正常稳定。\n- **使用建议**: 用户可继续正常开展日常业务工作。",
                        network_fix: "### 🌐 网络与互联网故障处理说明\n- **技术操作**: 重置 IP 地址配置、清理 DNS 缓存并优化局域网/无线网络连接。\n- **连通状态**: 互联网连接与本地服务器访问已恢复正常。\n- **测试结果**: 传输速度与 Ping 延迟稳定良好。",
                        printer_fix: "### 🖨️ 打印机与扫描仪故障处理说明\n- **处理措施**: 排除卡纸问题、清洁进纸胶轮并更换墨盒/碳粉。\n- **打印测试**: 打印效果清晰整洁，打印机队列无阻塞滞留。\n- **设备状态**: 打印设备已可正常投入使用。",
                        account_reset: "### 🔑 账号重置与权限更新说明\n- **处理措施**: 账号密码已重置为系统临时密码。\n- **用户指引**: 请使用临时密码登录系统，并在首次登录后立即修改新密码。\n- **访问权限**: 相关功能模块权限已核对并处于正常激活状态。",
                        sparepart_wait: "### 📦 备件与零配件采购进度说明\n- **部件状态**: 需更换硬件配件，目前已提交采购订单并处于配货调拨中。\n- **预计时间**: 预计在 1-3 个工作日内送达。\n- **应急方案**: 已部署临时替代方案以确保日常生产工作不受影响。",
                        resolved_confirm: "### ✅ 故障排除与处理完成确认\n- **解决说明**: 所反馈的技术故障已全数排查修复并经过现场测试。\n- **工单状态**: 此工单标记为已解决（Resolved）。\n- **补充说明**: 如后续仍有相关异常，请随时联系技术支持团队。"
                    };
                } else if (lang === 'en') {
                    return {
                        pc_fix: "### 🖥️ PC & Hardware Repair Solution\n- **Action Taken**: Device cleaning, system service restart, and driver verification.\n- **Inspection Results**: Components functioning normally and stably.\n- **Recommendation**: User may resume normal work operations.",
                        network_fix: "### 🌐 Network & Internet Resolution\n- **Technical Action**: Reset IP configuration, flush DNS cache, and optimized LAN/Wi-Fi connection.\n- **Connectivity Status**: Internet connection and local server access restored.\n- **Testing**: Speed and ping latency verified stable.",
                        printer_fix: "### 🖨️ Printer & Scanner Resolution\n- **Action Taken**: Paper jam cleared, roller cleaned, and toner/ink replaced.\n- **Test Print**: Printout sharp, clean, and no spooler queue stuck.\n- **Status**: Printer device ready for operational use.",
                        account_reset: "### 🔑 Account Reset & Access Renewal\n- **Action Taken**: Password has been reset to a temporary password.\n- **User Instructions**: Please sign in using the temporary password and update it immediately upon first login.\n- **Permissions**: Module access privileges verified and active.",
                        sparepart_wait: "### 📦 Spare Part Procurement Status\n- **Component Status**: Requires replacement part currently in procurement/ordering process.\n- **Estimated Time**: Expected to arrive within 1-3 business days.\n- **Temporary Action**: Temporary workaround implemented to avoid workflow disruption.",
                        resolved_confirm: "### ✅ Issue Resolution Confirmation\n- **Solution**: All reported technical issues have been resolved and tested.\n- **Ticket Status**: Issue marked as RESOLVED.\n- **Note**: If you encounter related issues, please contact technical support again."
                    };
                } else {
                    return {
                        pc_fix: "### 🖥️ Solusi Perbaikan PC & Hardware\n- **Tindakan Penanganan**: Pembersihan perangkat, restart service sistem, dan verifikasi driver.\n- **Hasil Pemeriksaan**: Komponen berfungsi normal dan stabil.\n- **Rekomendasi**: Pengguna dapat melanjutkan operasional pekerjaan.",
                        network_fix: "### 🌐 Penanganan Kendala Jaringan & Internet\n- **Tindakan Teknis**: Reset konfigurasi IP, flush DNS cache, dan optimasi koneksi LAN/Wi-Fi.\n- **Status Konektivitas**: Koneksi internet dan akses server lokal kembali normal.\n- **Pengujian**: Kecepatan dan stabilitas ping terverifikasi baik.",
                        printer_fix: "### 🖨️ Penanganan Printer & Scanner\n- **Tindakan**: Paper jam diatasi, pembersihan roller, dan penggantian toner/tinta.\n- **Uji Cetak (Test Print)**: Hasil cetak tajam, bersih, dan tidak ada antrean dokumen tertahan.\n- **Status**: Perangkat printer siap digunakan kembali.",
                        account_reset: "### 🔑 Reset Akun & Pembaharuan Akses\n- **Tindakan**: Kata sandi telah direset ke kata sandi sementara.\n- **Instruksi Pengguna**: Silakan login dengan kata sandi sementara dan segera ubah kata sandi saat pertama kali masuk.\n- **Hak Akses**: Izin akses modul telah diverifikasi dan aktif.",
                        sparepart_wait: "### 📦 Status Pengadaan Sparepart / Suku Cadang\n- **Status Komponen**: Memerlukan penggantian suku cadang yang saat ini dalam proses pengadaan/pemesanan.\n- **Estimasi Waktu**: Diperkirakan tiba dalam 1-3 hari kerja.\n- **Tindakan Sementara**: Telah disiapkan solusi alternatif sementara untuk menjaga kelancaran kerja.",
                        resolved_confirm: "### ✅ Konfirmasi Penyelesaian Masalah\n- **Solusi**: Seluruh kendala teknis yang dilaporkan telah dituntaskan dan diuji coba.\n- **Status Tiket**: Kendala dinyatakan SELESAI (Resolved).\n- **Catatan**: Jika masih mengalami kendala terkait, silakan hubungi tim teknis kembali."
                    };
                }
            },

            getSmartPolishAnswer(current) {
                const lang = this.getLocale();
                if (lang === 'zh') {
                    return "### 🛠️ 技术专员处理措施\n" + current + "\n\n### 🔍 技术分析与现场测试\n- 已对报告的故障现象进行系统性检测与排查\n- 运行参数已恢复至标准规格\n\n### 🟢 处理状态与使用建议\n- 业务操作故障已妥善排除\n- 用户可重新尝试操作并确认效果";
                } else if (lang === 'en') {
                    return "### 🛠️ Specialist Resolution Actions\n" + current + "\n\n### 🔍 Technical Analysis & Testing\n- A systematic verification has been performed for the reported issue\n- Operating parameters restored to standard baseline\n\n### 🟢 Status & User Recommendations\n- Operational issue has been addressed\n- User may test and verify operations";
                } else {
                    return "### 🛠️ Tindakan Penanganan Petugas\n" + current + "\n\n### 🔍 Analisis & Pengujian Teknis\n- Telah dilakukan pengecekan sistematis terhadap kendala yang dilaporkan\n- Parameter operasional telah dikembalikan ke kondisi standar\n\n### 🟢 Status & Rekomendasi Pengguna\n- Kendala operasional telah tertangani\n- Pengguna dapat mencoba kembali dan melakukan konfirmasi";
                }
            },

            clearContent() {
                const lang = this.getLocale();
                const msg = lang === 'zh'
                    ? '您确定要清空处理答复内容吗？'
                    : (lang === 'en' ? 'Are you sure you want to clear the response text?' : 'Apakah Anda yakin ingin mengosongkan teks tanggapan?');
                if (confirm(msg)) {
                    this.setText('');
                }
                this.closeMenus();
            },

            insertTemplate(type) {
                const templates = this.getTemplates();
                const tpl = templates[type] || templates['resolved_confirm'];
                const lang = this.getLocale();
                const confirmMsg = lang === 'zh'
                    ? '是否将此处理方案模板添加到回答内容中？'
                    : (lang === 'en' ? 'Append this solution template to the response?' : 'Tambahkan template ini ke dalam teks jawaban?');

                if (tpl) {
                    const el = this.$refs.textarea;
                    if (el && el.value.trim().length > 0) {
                        if (confirm(confirmMsg)) {
                            this.insertText('\n\n' + tpl);
                        }
                    } else {
                        this.setText(tpl);
                    }
                }
                this.closeMenus();
            },

            improveAnswer(action = 'smart_polish') {
                const el = this.$refs.textarea;
                const current = el ? el.value.trim() : '';

                if (!current) {
                    this.insertTemplate('resolved_confirm');
                    this.closeMenus();
                    return;
                }

                if (action === 'smart_polish') {
                    const formatted = this.getSmartPolishAnswer(current);
                    this.setText(formatted);
                } else if (action === 'bullets') {
                    const lines = current.split('\n').filter(l => l.trim().length > 0);
                    const bulleted = lines.map(l => l.startsWith('-') || l.startsWith('*') ? l : '- ' + l).join('\n');
                    this.setText(bulleted);
                } else if (action === 'clean') {
                    let clean = current
                        .replace(/^#+\s+/gm, '')
                        .replace(/\*\*([^*]+)\*\*/g, '$1')
                        .replace(/\*([^*]+)\*/g, '$1')
                        .replace(/`([^`]+)`/g, '$1')
                        .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
                    this.setText(clean);
                }
                this.closeMenus();
            },

            onKeydown(e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                    e.preventDefault();
                    this.wrapSelection('**', '**', 'teks tebal');
                } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'i') {
                    e.preventDefault();
                    this.wrapSelection('*', '*', 'teks miring');
                } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'u') {
                    e.preventDefault();
                    this.wrapSelection('<u>', '</u>', 'teks garis bawah');
                } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    this.undo();
                } else if ((e.ctrlKey || e.metaKey) && (e.key.toLowerCase() === 'y' || (e.key.toLowerCase() === 'z' && e.shiftKey))) {
                    e.preventDefault();
                    this.redo();
                }
            },

            renderMarkdown(text) {
                if (!text || !text.trim()) {
                    return '<p class="text-slate-400 dark:text-slate-500 italic text-xs">Belum ada tanggapan atau instruksi yang dimasukkan...</p>';
                }
                let html = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');

                // Headers
                html = html.replace(/^### (.*$)/gim, '<h3 class="font-bold text-sm text-slate-900 dark:text-white mt-2 mb-1">$1</h3>');
                html = html.replace(/^## (.*$)/gim, '<h2 class="font-bold text-base text-slate-900 dark:text-white mt-2.5 mb-1">$1</h2>');
                html = html.replace(/^# (.*$)/gim, '<h1 class="font-bold text-lg text-slate-900 dark:text-white mt-3 mb-1.5">$1</h1>');

                // Bold & Italic & Underline & Strike & Highlight
                html = html.replace(/\*\*([^*]+)\*\*/gim, '<strong class="font-bold text-slate-900 dark:text-white">$1</strong>');
                html = html.replace(/\*([^*]+)\*/gim, '<em class="italic text-slate-800 dark:text-slate-200">$1</em>');
                html = html.replace(/&lt;u&gt;(.*?)&lt;\/u&gt;/gim, '<u>$1</u>');
                html = html.replace(/~~([^~]+)~~/gim, '<del class="line-through text-slate-500">$1</del>');
                html = html.replace(/&lt;mark&gt;(.*?)&lt;\/mark&gt;/gim, '<mark class="bg-yellow-200 dark:bg-yellow-800 px-1 rounded text-slate-900 dark:text-white">$1</mark>');

                // Code block & inline code
                html = html.replace(/```([\s\S]*?)```/gim, '<pre class="bg-slate-900 text-slate-100 p-2.5 rounded-lg text-xs font-mono my-2 overflow-x-auto"><code>$1</code></pre>');
                html = html.replace(/`([^`]+)`/gim, '<code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-sky-600 dark:text-sky-400 font-mono text-xs">$1</code>');

                // Badges
                html = html.replace(/\[URGENT\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">URGENT</span>');
                html = html.replace(/\[WARNING\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">WARNING</span>');
                html = html.replace(/\[INFO\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">INFO</span>');
                html = html.replace(/\[SOLVED\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">SOLVED</span>');
                html = html.replace(/\[IN PROGRESS\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">IN PROGRESS</span>');

                // Checklists
                html = html.replace(/^- \[(x|X)\] (.*$)/gim, '<div class="flex items-center gap-1.5 text-xs text-slate-500 line-through my-0.5"><span class="text-emerald-500 font-bold">✓</span> $2</div>');
                html = html.replace(/^- \[ \] (.*$)/gim, '<div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 my-0.5"><span class="w-3.5 h-3.5 rounded border border-gray-400 dark:border-slate-600 inline-block"></span> $1</div>');

                // Bullets
                html = html.replace(/^- (.*$)/gim, '<li class="ml-4 list-disc text-xs text-slate-700 dark:text-slate-300 my-0.5">$1</li>');

                // Quotes
                html = html.replace(/^> (.*$)/gim, '<blockquote class="border-l-3 border-blue-500 pl-2.5 py-1 italic text-xs text-slate-600 dark:text-slate-400 my-1.5 bg-blue-50/50 dark:bg-blue-950/20 rounded-r">$1</blockquote>');

                // Markdown links: [text](url)
                html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 underline hover:text-blue-700">$1</a>');

                // Line breaks
                html = html.replace(/\n/gim, '<br>');
                return html;
            }
        };
    }
</script>
