<div class="bg-white dark:bg-slate-900 shadow-lg border border-gray-200 dark:border-slate-800 rounded-xl p-6 md:p-8 transition-colors">
    <div class="flex items-center justify-between pb-5 border-b border-gray-100 dark:border-slate-800 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Submit Support Request') }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Dispatch an official ticket to designated corporate department') }}</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            {{ __('Live Routing') }}
        </span>
    </div>

    @guest
        <div class="mb-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-900 dark:text-amber-200 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold">{{ __('Akses Tamu (Belum Login)') }}</p>
                    <p class="text-[11px] text-amber-800 dark:text-amber-300/90">{{ __('Anda dapat melihat daftar tiket dan statusnya. Untuk mengirim tiket baru, silakan masuk ke akun Anda.') }}</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="shrink-0 px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs text-center">
                {{ __('Masuk ke Akun') }}
            </a>
        </div>
    @endguest

    @if ($isSuccess)
        <div class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-between text-emerald-900 dark:text-emerald-200 transition-all duration-300">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs font-bold">{{ __('Request Dispatched Successfully') }}</p>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ __('Your ticket is active and linked to the communication desk on the right.') }}</p>
                </div>
            </div>
            <button wire:click="$set('isSuccess', false)" class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-200 cursor-pointer">{{ __('Dismiss') }}</button>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5">
        <!-- Input: Request Title -->
        <div>
            <label for="ticket_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Request Title') }}</label>
            <input id="ticket_title" type="text" wire:model="title" placeholder="{{ __('Brief summary of the issue or requirement (e.g., VPN connection drops on Floor 3)') }}"
                class="w-full px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500" />
            @error('title') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>

        <!-- 3 Select Dropdowns: Sender Department, Target Department, Request Category -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Select Dropdown: Sender Department -->
            <div>
                <label for="sender_department_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Sender Department') }}</label>
                <select id="sender_department_id" wire:model="sender_department_id" 
                    class="w-full px-3 py-2.5 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('sender_department_id') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Select Dropdown: Target Department -->
            <div>
                <label for="target_department_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Target Department') }}</label>
                <select id="target_department_id" wire:model.live="target_department_id" 
                    class="w-full px-3 py-2.5 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    @foreach ($targetDepartments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('target_department_id') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Select Dropdown: Request Category (Dependent on Target Department) -->
            <div>
                <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Request Category') }}</label>
                <select id="category" wire:model="category"
                    class="w-full px-3 py-2.5 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    @foreach ($this->availableCategories as $cat)
                        <option value="{{ $cat }}">{{ __($cat) }}</option>
                    @endforeach
                </select>
                @error('category') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Textarea: Problem Details / Description -->
        <div>
            <label for="ticket_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Problem Details / Description') }}</label>
            <textarea id="ticket_description" wire:model="description" rows="4" placeholder="{{ __('Detail the situation, asset tag, affected personnel, error messages, and steps already attempted...') }}"
                class="w-full px-3.5 py-2.5 text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500"></textarea>
            @error('description') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>

        <!-- Optional Photo Evidence Upload (Bukti Foto) -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="ticket_photo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    {{ __('Bukti Foto / Lampiran (Opsional)') }}
                </label>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                    {{ __('Maks. 10MB (JPG, PNG, WEBP)') }}
                </span>
            </div>

            @if ($photo)
                <div class="p-3.5 rounded-xl border border-blue-200 dark:border-blue-800/80 bg-blue-50/60 dark:bg-blue-950/30 flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        @if (method_exists($photo, 'temporaryUrl'))
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview Bukti Foto" class="w-14 h-14 rounded-lg object-cover border border-blue-300 dark:border-blue-700 shadow-xs shrink-0" />
                        @else
                            <div class="w-14 h-14 rounded-lg bg-blue-100 dark:bg-blue-900/60 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $photo->getClientOriginalName() }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ round($photo->getSize() / 1024, 1) }} KB • <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ __('Foto Siap Diunggah') }}</span></p>
                        </div>
                    </div>
                    <button type="button" wire:click="removePhoto" class="px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/60 transition cursor-pointer shrink-0 flex items-center gap-1 border border-rose-200 dark:border-rose-900">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>{{ __('Hapus Foto') }}</span>
                    </button>
                </div>
            @else
                <div class="relative border-2 border-dashed border-gray-300 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-400 rounded-xl p-4 text-center transition bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer group">
                    <input id="ticket_photo" type="file" wire:model="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                    <div class="flex flex-col items-center justify-center pointer-events-none">
                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform flex items-center justify-center mb-2 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <span class="text-blue-600 dark:text-blue-400 font-bold underline decoration-blue-400/50 underline-offset-2">{{ __('Pilih Bukti Foto') }}</span> {{ __('atau tarik file gambar ke sini') }}
                        </p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                            {{ __('Tangkapan layar error, foto fisik alat, dsb. (Opsional)') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Loading indicator when uploading photo -->
            <div wire:loading wire:target="photo" class="mt-2 text-xs text-blue-600 dark:text-blue-400 flex items-center gap-2">
                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span>{{ __('Sedang memproses & mengunggah foto...') }}</span>
            </div>

            @error('photo') 
                <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Priority Level Badges -->
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Priority Level') }}</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @php
                    $priorities = [
                        'Low' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-700 dark:hover:text-emerald-300', 'active' => 'bg-emerald-600 text-white border-emerald-600 shadow-sm'],
                        'Medium' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/50 hover:text-blue-700 dark:hover:text-blue-300', 'active' => 'bg-blue-600 text-white border-blue-600 shadow-sm'],
                        'High' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-amber-950/50 hover:text-amber-700 dark:hover:text-amber-300', 'active' => 'bg-amber-500 text-white border-amber-500 shadow-sm'],
                        'Critical' => ['base' => 'border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-950/50 hover:text-rose-700 dark:hover:text-rose-300', 'active' => 'bg-rose-600 text-white border-rose-600 shadow-sm'],
                    ];
                @endphp
                @foreach ($priorities as $level => $styles)
                    <button type="button" wire:click="setPriority('{{ $level }}')" 
                        class="py-2.5 px-3 text-xs font-bold rounded-lg border text-center transition cursor-pointer {{ $priority === $level ? $styles['active'] : $styles['base'] }}">
                        {{ __($level) }}
                    </button>
                @endforeach
            </div>
            @error('priority') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button: Solid Corporate Blue, hover effect, standard rounded corners -->
        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-gray-100 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ __('Dispatching instantly initiates the inter-department communication thread') }}</span>
            @auth
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>{{ __('Submit Request') }}</span>
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>{{ __('Submitting Request...') }}</span>
                    </span>
                </button>
            @else
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-md flex items-center justify-center gap-2 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>{{ __('Masuk (Login) untuk Kirim Tiket') }}</span>
                </a>
            @endauth
        </div>
    </form>

    <!-- ========================================== -->
    <!-- FEATURE 2: "My Tickets" Table & Delete Feature -->
    <!-- ========================================== -->
    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center justify-between mb-3.5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('My Tickets') }}</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ __('Daftar tiket yang Anda ajukan beserta status penanganannya') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if (session()->has('ticket_updated'))
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800 animate-fade-in flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('ticket_updated') }}
                    </span>
                @endif
                @if (session()->has('ticket_deleted'))
                    <span class="text-xs text-rose-600 dark:text-rose-400 font-semibold bg-rose-50 dark:bg-rose-950/40 px-3 py-1 rounded-lg border border-rose-200 dark:border-rose-800 animate-fade-in">
                        {{ session('ticket_deleted') }}
                    </span>
                @endif
                @if (session()->has('ticket_error'))
                    <span class="text-xs text-rose-600 dark:text-rose-400 font-semibold bg-rose-50 dark:bg-rose-950/40 px-3 py-1 rounded-lg border border-rose-200 dark:border-rose-800 animate-fade-in">
                        {{ session('ticket_error') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                    <tr>
                        <th scope="col" class="py-3 px-3.5">{{ __('Ticket ID') }}</th>
                        <th scope="col" class="py-3 px-3.5">{{ __('Title') }}</th>
                        <th scope="col" class="py-3 px-3.5">{{ __('Target Department') }}</th>
                        <th scope="col" class="py-3 px-3.5">{{ __('Category') }}</th>
                        <th scope="col" class="py-3 px-3.5">{{ __('Priority') }}</th>
                        <th scope="col" class="py-3 px-3.5">{{ __('Status') }}</th>
                        <th scope="col" class="py-3 px-3.5 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                    @forelse ($myTickets as $ticket)
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
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-3.5 font-mono font-bold text-slate-500 dark:text-slate-400">
                                #{{ $ticket->id }}
                            </td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white max-w-[200px] truncate" title="{{ $ticket->title }}">
                                {{ $ticket->title }}
                            </td>
                            <td class="py-3 px-3.5 text-slate-600 dark:text-slate-300">
                                {{ $ticket->targetDepartment->name ?? '-' }}
                            </td>
                            <td class="py-3 px-3.5">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium border border-gray-200 dark:border-slate-700 text-[11px]">
                                    {{ $ticket->category }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5">
                                <span class="px-2 py-0.5 rounded border font-bold text-[10px] {{ $priorityStyle }}">
                                    {{ __($ticket->priority) }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5">
                                <span class="px-2 py-0.5 rounded border font-semibold text-[10px] {{ $statusStyle }}">
                                    {{ __($ticket->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5 text-right whitespace-nowrap space-x-1 sm:space-x-2">
                                @if ($ticket->status === 'Pending')
                                    <!-- Update/Edit Button (Belum di-acc Admin) -->
                                    <button type="button" 
                                        wire:click="openEditModal({{ $ticket->id }})" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-bold cursor-pointer transition inline-flex items-center gap-1 text-xs px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-950/40"
                                        title="{{ __('Edit laporan (posisi belum di-acc admin)') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>{{ __('Edit') }}</span>
                                    </button>

                                    <!-- Delete Button (Belum di-acc Admin) -->
                                    <button type="button" 
                                        wire:click="deleteTicket({{ $ticket->id }})" 
                                        wire:confirm="{{ __('Apakah Anda yakin ingin membatalkan & menghapus laporan ini?') }}"
                                        class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 font-semibold cursor-pointer transition inline-flex items-center gap-1 text-xs px-2 py-1 rounded hover:bg-rose-50 dark:hover:bg-rose-950/40"
                                        title="{{ __('Hapus laporan (belum di-acc admin)') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>{{ __('Delete') }}</span>
                                    </button>
                                @else
                                    <!-- Terkunci jika sudah di-acc admin -->
                                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 dark:text-slate-500 font-medium" title="{{ __('Laporan sudah di-acc oleh Admin sehingga tidak dapat diedit atau dihapus.') }}">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        <span>{{ __('Terkunci (Di-ACC Admin)') }}</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                <p class="text-xs">{{ __('Belum ada tiket yang diajukan oleh akun Anda.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- EDIT TICKET MODAL -->
    <!-- ========================================== -->
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
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ __('Update Tiket') }} #{{ $editingTicketId }}</span>
                            </h3>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ __('Perbarui informasi kendala atau status tiket dukungan Anda') }}</p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeEditModal" 
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Form) -->
                <form wire:submit.prevent="updateTicket" class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Title -->
                    <div>
                        <label for="edit_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Issue Title') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="edit_title" wire:model="editTitle"
                               class="w-full px-3.5 py-2 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                        @error('editTitle') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Target Department & Category (Dependent Dropdown) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_target_dept" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ __('Target Department') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="edit_target_dept" wire:model.live="editTargetDepartmentId"
                                    class="w-full px-3 py-2 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                                @foreach($targetDepartments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('editTargetDepartmentId') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="edit_category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ __('Category') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="edit_category" wire:model="editCategory"
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
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">{{ __('Priority Level') }}</label>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ __('Status Tiket:') }} <strong class="text-blue-600 dark:text-blue-400 font-bold">{{ $editStatus }}</strong> <span class="text-[10px] text-slate-400">({{ __('Diperbarui oleh Admin') }})</span>
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach ($priorities as $level => $styles)
                                <button type="button" wire:click="setEditPriority('{{ $level }}')" 
                                        class="py-2.5 px-2 text-xs font-bold rounded-lg border text-center transition cursor-pointer {{ $editPriority === $level ? $styles['active'] : $styles['base'] }}">
                                    {{ __($level) }}
                                </button>
                            @endforeach
                        </div>
                        @error('editPriority') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="edit_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Detailed Description') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="edit_description" wire:model="editDescription" rows="3"
                                  class="w-full px-3.5 py-2.5 text-xs md:text-sm text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/90 rounded-lg border border-gray-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs leading-relaxed"></textarea>
                        @error('editDescription') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Photo Attachment -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Bukti Foto Kendala (Opsional)') }}
                        </label>
                        
                        <!-- Existing Photo Preview if available -->
                        @if ($existingPhotoUrl && ! $removeExistingPhoto)
                            <div class="mb-3 p-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $existingPhotoUrl }}" alt="Foto Tiket" class="w-12 h-12 rounded-lg object-cover border border-gray-300 dark:border-slate-600 shadow-xs">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ __('Foto Tersimpan Saat Ini') }}</p>
                                        <span class="text-[11px] text-slate-400">{{ __('Foto akan tetap dipertahankan kecuali diganti/dihapus') }}</span>
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
                                    <img src="{{ $editPhoto->temporaryUrl() }}" alt="New Photo Preview" class="w-12 h-12 rounded-lg object-cover border border-blue-300 dark:border-blue-700 shadow-xs">
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('Simpan Perubahan') }}</span>
                            </span>
                            <span wire:loading wire:target="updateTicket" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>{{ __('Menyimpan...') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
