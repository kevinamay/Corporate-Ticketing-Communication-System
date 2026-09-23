<div class="bg-white shadow-lg border border-gray-200 rounded-xl p-6 md:p-8">
    <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Submit Support Request</h2>
                <p class="text-xs text-slate-500">Dispatch an official ticket to designated corporate department</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            Live Routing
        </span>
    </div>

    @guest
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-900 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold">Akses Tamu (Belum Login)</p>
                    <p class="text-[11px] text-amber-800">Anda dapat melihat daftar tiket dan statusnya. Untuk mengirim tiket baru, silakan masuk ke akun Anda.</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="shrink-0 px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs text-center">
                Masuk ke Akun
            </a>
        </div>
    @endguest

    @if ($isSuccess)
        <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-900 transition-all duration-300">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs font-bold">Request Dispatched Successfully</p>
                    <p class="text-xs text-emerald-700">Your ticket is active and linked to the communication desk on the right.</p>
                </div>
            </div>
            <button wire:click="$set('isSuccess', false)" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 cursor-pointer">Dismiss</button>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5">
        <!-- Input: Request Title -->
        <div>
            <label for="ticket_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Request Title</label>
            <input id="ticket_title" type="text" wire:model="title" placeholder="Brief summary of the issue or requirement (e.g., VPN connection drops on Floor 3)"
                class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs placeholder:text-slate-400" />
            @error('title') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>

        <!-- 3 Select Dropdowns: Sender Department, Target Department, Request Category -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Select Dropdown: Sender Department -->
            <div>
                <label for="sender_department_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Sender Department</label>
                <select id="sender_department_id" wire:model="sender_department_id" 
                    class="w-full px-3 py-2.5 text-xs md:text-sm text-slate-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('sender_department_id') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Select Dropdown: Target Department -->
            <div>
                <label for="target_department_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Target Department</label>
                <select id="target_department_id" wire:model="target_department_id" 
                    class="w-full px-3 py-2.5 text-xs md:text-sm text-slate-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('target_department_id') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Select Dropdown: Request Category -->
            <div>
                <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Request Category</label>
                <select id="category" wire:model="category" wire:change="setCategory($event.target.value)"
                    class="w-full px-3 py-2.5 text-xs md:text-sm text-slate-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    <option value="IT">IT Support</option>
                    <option value="HR">Human Resources</option>
                    <option value="Maintenance">Facility & Maintenance</option>
                    <option value="General">General Operations</option>
                </select>
                @error('category') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Textarea: Problem Details / Description -->
        <div>
            <label for="ticket_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Problem Details / Description</label>
            <textarea id="ticket_description" wire:model="description" rows="4" placeholder="Detail the situation, asset tag, affected personnel, error messages, and steps already attempted..."
                class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs placeholder:text-slate-400"></textarea>
            @error('description') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
        </div>

        <!-- Priority Level Badges & Ticket Status Select -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- Radio/Badges: Priority Level -->
            <div class="md:col-span-8">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Priority Level</label>
                <div class="grid grid-cols-4 gap-2">
                    @php
                        $priorities = [
                            'Low' => ['base' => 'border-gray-200 bg-slate-50 text-slate-700 hover:bg-emerald-50 hover:text-emerald-700', 'active' => 'bg-emerald-600 text-white border-emerald-600 shadow-sm'],
                            'Medium' => ['base' => 'border-gray-200 bg-slate-50 text-slate-700 hover:bg-blue-50 hover:text-blue-700', 'active' => 'bg-blue-600 text-white border-blue-600 shadow-sm'],
                            'High' => ['base' => 'border-gray-200 bg-slate-50 text-slate-700 hover:bg-amber-50 hover:text-amber-700', 'active' => 'bg-amber-500 text-white border-amber-500 shadow-sm'],
                            'Critical' => ['base' => 'border-gray-200 bg-slate-50 text-slate-700 hover:bg-rose-50 hover:text-rose-700', 'active' => 'bg-rose-600 text-white border-rose-600 shadow-sm'],
                        ];
                    @endphp
                    @foreach ($priorities as $level => $styles)
                        <button type="button" wire:click="setPriority('{{ $level }}')" 
                            class="py-2 px-2 text-xs font-bold rounded-lg border text-center transition cursor-pointer {{ $priority === $level ? $styles['active'] : $styles['base'] }}">
                            {{ $level }}
                        </button>
                    @endforeach
                </div>
                @error('priority') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Select: Ticket Status -->
            <div class="md:col-span-4">
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Initial Status</label>
                <select id="status" wire:model="status" 
                    class="w-full px-3 py-2 text-xs md:text-sm text-slate-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs">
                    <option value="Pending">Pending</option>
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                </select>
                @error('status') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Submit Button: Solid Corporate Blue, hover effect, standard rounded corners -->
        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-gray-100">
            <span class="text-xs text-slate-500">Dispatching instantly initiates the inter-department communication thread</span>
            @auth
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Submit Request</span>
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Submitting Request...</span>
                    </span>
                </button>
            @else
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-md flex items-center justify-center gap-2 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Masuk (Login) untuk Kirim Tiket</span>
                </a>
            @endauth
        </div>
    </form>
</div>
