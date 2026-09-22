<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" type="button" 
        class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md flex items-center gap-3 cursor-pointer transition text-white">
        <div class="relative">
            <img src="{{ $currentUser?->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($currentUser?->name ?? 'User') . '&background=0284c7&color=fff' }}" 
                 alt="{{ $currentUser?->name ?? 'User' }}" 
                 class="w-8 h-8 rounded-full object-cover border border-white/40 shadow-xs" />
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border border-white rounded-full"></span>
        </div>
        <div class="text-left hidden sm:block">
            <div class="flex items-center gap-1.5">
                <p class="text-xs font-semibold text-white leading-tight">{{ $currentUser?->name ?? 'Corporate User' }}</p>
                <span class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded bg-blue-500/40 text-blue-100 border border-blue-400/30">
                    {{ $currentUser?->role ?? 'Staff' }}
                </span>
            </div>
            <p class="text-[10px] text-blue-200 leading-tight">{{ $currentUser?->department?->name ?? 'General Staff' }}</p>
        </div>
        <svg class="w-3.5 h-3.5 text-blue-200 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
         class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-200 p-2 z-50 text-slate-800"
         style="display: none;">
        <div class="px-3 py-2 border-b border-gray-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Switch Perspective (Corporate Roster)
        </div>
        <div class="space-y-1 mt-1 max-h-64 overflow-y-auto">
            @foreach ($users as $user)
                <button wire:click="switchUser({{ $user->id }})" @click="open = false" 
                    class="w-full px-2.5 py-2 rounded-lg flex items-center gap-2.5 text-left transition {{ $user->id === $currentUser?->id ? 'bg-blue-50 text-blue-900 font-bold border border-blue-200' : 'hover:bg-slate-50 text-slate-700' }}">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                         alt="{{ $user->name }}" 
                         class="w-7 h-7 rounded-full object-cover border border-gray-200" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs truncate">{{ $user->name }}</span>
                            <span class="text-[9px] uppercase px-1 rounded bg-slate-100 text-slate-600 font-medium">{{ $user->role }}</span>
                        </div>
                        <p class="text-[10px] text-slate-400 truncate">{{ $user->department?->name ?? 'Corporate HQ' }}</p>
                    </div>
                </button>
            @endforeach
        </div>
        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between px-2 text-xs">
            @auth
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-1.5 px-3 rounded-lg text-rose-600 hover:bg-rose-50 font-semibold transition text-left flex items-center justify-between">
                        <span>Keluar Akun (Logout)</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-bold">Masuk (Login)</a>
                <a href="{{ route('register') }}" class="text-slate-600 hover:text-slate-800">Daftar Akun Baru</a>
            @endauth
        </div>
    </div>
</div>
