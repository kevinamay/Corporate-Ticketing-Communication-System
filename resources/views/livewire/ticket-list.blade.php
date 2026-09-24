<div class="bg-white dark:bg-slate-900 shadow-md border border-gray-200 dark:border-slate-800 rounded-xl p-5 mb-6 transition-colors" wire:poll.3s>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Active Incident & Request Queue</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Select any record to link into the live communication desk</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="w-full sm:w-64">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search ticket title or details..."
                class="w-full px-3 py-1.5 text-xs text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500" />
        </div>
    </div>

    <!-- Filters Row -->
    <div class="flex flex-wrap items-center gap-2 mb-4 pb-3 border-b border-gray-100 dark:border-slate-800 text-xs">
        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status:</span>
        <button wire:click="$set('statusFilter', 'all')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            All
        </button>
        <button wire:click="$set('statusFilter', 'Pending')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'Pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            Pending
        </button>
        <button wire:click="$set('statusFilter', 'Open')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'Open' ? 'bg-blue-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            Open
        </button>
        <button wire:click="$set('statusFilter', 'In Progress')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'In Progress' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            In Progress
        </button>
        <button wire:click="$set('statusFilter', 'Resolved')" 
            class="px-2.5 py-1 rounded-md font-semibold text-xs transition cursor-pointer {{ $statusFilter === 'Resolved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
            Resolved
        </button>

        <div class="h-4 w-[1px] bg-gray-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>

        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Department:</span>
        <select wire:model.live="departmentFilter" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs text-slate-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700 focus:ring-1 focus:ring-blue-500 focus:outline-none">
            <option value="all">All Departments</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Ticket Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse ($tickets as $t)
            @php
                $isSelected = $selectedTicketId === $t->id;
                $priorityBadge = match($t->priority) {
                    'Critical' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                    'High' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                    'Medium' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                    default => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                };
            @endphp
            <div wire:click="selectTicket({{ $t->id }})" 
                class="p-4 rounded-xl cursor-pointer transition border text-left {{ $isSelected ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-500 dark:border-blue-500 ring-2 ring-blue-500/20 shadow-md' : 'bg-white dark:bg-slate-800/80 border-gray-200 dark:border-slate-700/80 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-xs' }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $priorityBadge }}">
                        {{ $t->priority }}
                    </span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">#{{ $t->id }}</span>
                </div>

                <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-1 mb-1">{{ $t->title }}</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mb-3">{{ $t->description }}</p>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-slate-700/50 text-[10px] text-slate-400 dark:text-slate-500">
                    <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[120px]">
                        {{ $t->targetDepartment->name }}
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 font-semibold text-slate-600 dark:text-slate-400">
                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            {{ $t->messages->count() }}
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700/70 text-slate-700 dark:text-slate-300 font-medium border border-gray-200 dark:border-slate-600">
                            {{ $t->status }}
                        </span>
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
                <h4 class="text-sm font-bold text-slate-800 dark:text-white">Belum Ada Tiket yang Dibuat</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">Antrean tiket antar divisi saat ini masih bersih. Silakan buat tiket pertama Anda melalui formulir di atas untuk memulai koordinasi antar divisi.</p>
            </div>
        @endforelse
    </div>
</div>
