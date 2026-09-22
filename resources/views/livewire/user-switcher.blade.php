<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" type="button" 
        class="clay-button px-3.5 py-2 rounded-2xl bg-white flex items-center gap-3 cursor-pointer border border-slate-100 hover:border-pink-200 transition">
        <div class="relative">
            <img src="{{ $currentUser?->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($currentUser?->name ?? 'Guest') }}" 
                 alt="{{ $currentUser?->name ?? 'User' }}" 
                 class="w-9 h-9 rounded-full object-cover border-2 border-pink-200 shadow-sm" />
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
        </div>
        <div class="text-left hidden md:block">
            <div class="flex items-center gap-1.5">
                <p class="text-xs font-bold text-slate-800 leading-tight">{{ $currentUser?->name ?? 'Corporate User' }}</p>
                <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.2 rounded bg-pink-100 text-pink-700">
                    {{ $currentUser?->role ?? 'Staff' }}
                </span>
            </div>
            <p class="text-[10px] text-slate-400 leading-tight">{{ $currentUser?->department?->name ?? 'Corporate HQ' }}</p>
        </div>
        <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" @click.away="open = false" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-72 clay-card bg-white p-2 shadow-2xl z-50 border border-slate-100"
         style="display: none;">
        <div class="px-3 py-2 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
            Switch Perspective (Demo)
        </div>
        <div class="space-y-1 mt-1 max-h-64 overflow-y-auto">
            @foreach ($users as $user)
                <button wire:click="switchUser({{ $user->id }})" @click="open = false" 
                    class="w-full px-3 py-2 rounded-xl flex items-center gap-3 text-left transition {{ $user->id === $currentUser->id ? 'bg-pink-50 text-pink-900 font-bold' : 'hover:bg-slate-50 text-slate-700' }}">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                         alt="{{ $user->name }}" 
                         class="w-8 h-8 rounded-full object-cover border border-slate-200" />
                    <div class="flex-1 truncate">
                        <div class="flex items-center justify-between">
                            <span class="text-xs">{{ $user->name }}</span>
                            <span class="text-[9px] uppercase px-1 rounded bg-slate-100 text-slate-500 font-semibold">{{ $user->role }}</span>
                        </div>
                        <p class="text-[10px] text-slate-400 truncate">{{ $user->department->name ?? 'Corporate Staff' }}</p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</div>
