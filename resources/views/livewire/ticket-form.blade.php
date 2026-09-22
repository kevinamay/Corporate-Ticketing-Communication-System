<div class="clay-card p-6 md:p-8 bg-white relative overflow-hidden">
    <!-- Subtle top decorative gradient glow -->
    <div class="absolute -top-12 -right-12 w-40 h-40 bg-pink-100 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-2xl bg-pink-100 text-pink-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </span>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Create Support Ticket</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">Submit an incident or service request directly to the responsible team</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
            Real-Time Sync
        </span>
    </div>

    @if ($isSuccess)
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 transition-all duration-300">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold">Ticket Created Successfully!</p>
                    <p class="text-xs text-emerald-600">The chat pane on the right is now linked to your new ticket.</p>
                </div>
            </div>
            <button wire:click="$set('isSuccess', false)" class="text-emerald-500 hover:text-emerald-700 text-sm font-medium">Dismiss</button>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5">
        <!-- Input: Request Title -->
        <div>
            <label for="ticket_title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Request Title</label>
            <input id="ticket_title" type="text" wire:model="title" placeholder="e.g., VPN connection drops intermittently on macOS Sequoia"
                class="clay-inset w-full px-4 py-3 text-sm text-slate-800 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition" />
            @error('title') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 3 Select Dropdowns: Sender Department, Target Department, Request Category -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Select Dropdown: Sender Department -->
            <div>
                <label for="sender_department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Sender Department</label>
                <div class="relative">
                    <select id="sender_department_id" wire:model="sender_department_id" 
                        class="clay-inset w-full px-3.5 py-3 text-xs md:text-sm text-slate-800 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition">
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('sender_department_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Select Dropdown: Target Department -->
            <div>
                <label for="target_department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Target Department</label>
                <div class="relative">
                    <select id="target_department_id" wire:model="target_department_id" 
                        class="clay-inset w-full px-3.5 py-3 text-xs md:text-sm text-slate-800 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition">
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('target_department_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Select Dropdown: Request Category -->
            <div>
                <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Request Category</label>
                <div class="relative">
                    <select id="category" wire:model="category" wire:change="setCategory($event.target.value)"
                        class="clay-inset w-full px-3.5 py-3 text-xs md:text-sm text-slate-800 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition">
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                @error('category') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Textarea: Problem Details / Description -->
        <div>
            <label for="ticket_description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Problem Details / Description</label>
            <textarea id="ticket_description" wire:model="description" rows="4" placeholder="Describe the problem, affected systems, location, and symptoms in detail..."
                class="clay-inset w-full px-4 py-3 text-sm text-slate-800 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition resize-none"></textarea>
            @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Priority Level (Selectable Pill Badges) & Ticket Status Select -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <!-- Radio/Badges: Priority Level -->
            <div class="md:col-span-8">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Priority Level</label>
                <div class="grid grid-cols-4 gap-2">
                    @php
                        $priorities = [
                            'Low' => ['bg' => 'hover:bg-emerald-50 text-emerald-700', 'active' => '!bg-emerald-500 !text-white'],
                            'Medium' => ['bg' => 'hover:bg-blue-50 text-blue-700', 'active' => '!bg-blue-500 !text-white'],
                            'High' => ['bg' => 'hover:bg-amber-50 text-amber-700', 'active' => '!bg-amber-500 !text-white'],
                            'Critical' => ['bg' => 'hover:bg-rose-50 text-rose-700', 'active' => '!bg-rose-600 !text-white'],
                        ];
                    @endphp
                    @foreach ($priorities as $level => $styles)
                        <button type="button" wire:click="setPriority('{{ $level }}')" 
                            class="clay-pill py-2.5 px-2 rounded-xl text-xs font-bold text-center cursor-pointer transition {{ $priority === $level ? $styles['active'] : 'bg-slate-50 ' . $styles['bg'] }}">
                            {{ $level }}
                        </button>
                    @endforeach
                </div>
                @error('priority') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Select: Ticket Status -->
            <div class="md:col-span-4">
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Ticket Status</label>
                <div class="relative">
                    <select id="status" wire:model="status" 
                        class="clay-inset w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-slate-50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition">
                        <option value="Pending">Pending</option>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Submit Button: Soft pastel pink, large, rounded -->
        <div class="pt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <span class="text-xs text-slate-400">Submitting will immediately register the ticket and initiate the real-time chat room</span>
            <button type="submit" wire:loading.attr="disabled"
                class="clay-button-pink w-full sm:w-auto px-8 py-3.5 rounded-2xl text-sm font-bold flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 shadow-lg">
                <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Submit Request</span>
                </span>
                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Processing Request...</span>
                </span>
            </button>
        </div>
    </form>
</div>
