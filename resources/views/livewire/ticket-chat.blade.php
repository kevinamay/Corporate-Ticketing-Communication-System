<div class="clay-card h-full flex flex-col bg-white overflow-hidden" wire:poll.3s>
    @if ($ticket)
        <!-- Chat Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <span class="w-10 h-10 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-base shadow-sm">
                        {{ strtoupper(substr($ticket->targetDepartment->name, 0, 2)) }}
                    </span>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-800 text-base leading-tight">{{ $ticket->targetDepartment->name }}</h3>
                        <!-- Status Badge -->
                        @php
                            $statusClasses = [
                                'Pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'Open' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'In Progress' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'Resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            ];
                        @endphp
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $statusClasses[$ticket->status] ?? 'bg-slate-100' }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 truncate max-w-[200px] md:max-w-xs" title="{{ $ticket->title }}">
                        #{{ $ticket->id }} - {{ $ticket->title }}
                    </p>
                </div>
            </div>

            <!-- Action Controls: Status Switcher & Voice Call -->
            <div class="flex items-center gap-2">
                <!-- Status Switcher Dropdown -->
                <select wire:change="updateStatus($event.target.value)" class="text-xs font-semibold px-2 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 shadow-sm focus:outline-none">
                    <option value="Pending" @selected($ticket->status === 'Pending')>Pending</option>
                    <option value="Open" @selected($ticket->status === 'Open')>Open</option>
                    <option value="In Progress" @selected($ticket->status === 'In Progress')>In Progress</option>
                    <option value="Resolved" @selected($ticket->status === 'Resolved')>Resolved</option>
                </select>

                <!-- Voice Call / Phone Button -->
                <button wire:click="startCall" title="Start Direct Audio Call"
                    class="clay-button p-2.5 rounded-xl bg-pink-50 text-pink-600 hover:bg-pink-100 hover:text-pink-700 cursor-pointer flex items-center justify-center transition">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Voice Call Modal Simulation -->
        @if ($isCalling)
            <div class="p-4 bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 text-white flex items-center justify-between shadow-inner animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center w-9 h-9 rounded-full bg-white/20 animate-ping"></div>
                    <div class="-ml-12 flex items-center justify-center w-9 h-9 rounded-full bg-white text-pink-600 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold tracking-wide">Audio Line Connected</p>
                        <p class="text-xs text-pink-100">{{ $ticket->targetDepartment->name }} Dispatch • <span class="font-mono">00:14</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="endCall" class="px-3 py-1.5 rounded-xl bg-white text-rose-600 font-bold text-xs shadow hover:bg-rose-50">
                        End Call
                    </button>
                </div>
            </div>
        @endif

        <!-- Ticket Summary Quick Banner -->
        <div class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Priority: <strong class="text-slate-700">{{ $ticket->priority }}</strong></span>
            <span>Category: <strong class="text-slate-700">{{ $ticket->category }}</strong></span>
            <span>Sender: <strong class="text-slate-700">{{ $ticket->sender->name }}</strong></span>
        </div>

        <!-- Chat Messages Container -->
        <div id="chat-box" class="flex-1 p-5 overflow-y-auto space-y-4 bg-slate-50/30 min-h-[360px] max-h-[500px]">
            <!-- System Ticket Origin Notice -->
            <div class="flex justify-center my-2">
                <div class="px-4 py-2 rounded-2xl bg-white border border-slate-100 shadow-sm text-center max-w-md">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Ticket Original Description</span>
                    <p class="text-xs text-slate-700 mt-1 italic">"{{ $ticket->description }}"</p>
                </div>
            </div>

            @forelse ($ticket->messages as $msg)
                @php
                    $isMe = $msg->user_id === $activeUserId;
                @endphp
                <div class="flex items-end gap-2.5 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    @if (! $isMe)
                        <img src="{{ $msg->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) }}" 
                             alt="{{ $msg->user->name }}" 
                             class="w-8 h-8 rounded-full border border-white shadow-sm object-cover shrink-0" />
                    @endif

                    <div class="max-w-[78%]">
                        <div class="flex items-center gap-2 mb-1 px-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <span class="text-[11px] font-bold text-slate-600">{{ $isMe ? 'You (' . $msg->user->name . ')' : $msg->user->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $msg->created_at->format('H:i') }}</span>
                        </div>

                        <div class="p-3.5 text-xs md:text-sm leading-relaxed {{ $isMe ? 'clay-bubble-sender' : 'clay-bubble-receiver' }}">
                            {{ $msg->message }}
                        </div>
                    </div>

                    @if ($isMe)
                        <img src="{{ $msg->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) }}" 
                             alt="{{ $msg->user->name }}" 
                             class="w-8 h-8 rounded-full border border-pink-200 shadow-sm object-cover shrink-0" />
                    @endif
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-full bg-pink-50 text-pink-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-600">No communication history yet</p>
                    <p class="text-[11px] text-slate-400">Send a message below to start real-time coordination</p>
                </div>
            @endforelse
        </div>

        <!-- Chat Input Footer -->
        <div class="p-4 border-t border-slate-100 bg-white">
            <form wire:submit="sendMessage" class="flex items-center gap-3">
                <input type="text" wire:model="newMessage" placeholder="Type message to {{ $ticket->targetDepartment->name }}..."
                    class="clay-inset flex-1 px-4 py-3 text-sm text-slate-800 bg-slate-50 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 transition" />
                <button type="submit" 
                    class="clay-button-pink px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Send</span>
                </button>
            </form>
            <div class="flex items-center justify-between mt-2 px-1 text-[10px] text-slate-400">
                <span>Real-time channel: Auto-updates every 3s</span>
                <span>Press Enter to send</span>
            </div>
        </div>
    @else
        <!-- Empty State when no ticket selected -->
        <div class="flex flex-col items-center justify-center h-full p-8 text-center min-h-[450px]">
            <div class="w-16 h-16 rounded-3xl bg-pink-100 text-pink-500 flex items-center justify-center mb-4 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Select or Create a Ticket</h3>
            <p class="text-xs text-slate-500 max-w-xs mt-1">
                Choose an existing ticket from the queue or submit a new ticket on the left to activate the real-time communications console.
            </p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        const scrollToBottom = () => {
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        };
        scrollToBottom();
        Livewire.on('messageSent', () => {
            setTimeout(scrollToBottom, 50);
        });
    });
</script>
