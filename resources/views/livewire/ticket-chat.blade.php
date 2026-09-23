<div class="bg-white shadow-lg border border-gray-200 rounded-xl h-full flex flex-col overflow-hidden" wire:poll.3s>
    @if ($ticket)
        <!-- Chat Header -->
        <div class="p-4 border-b border-gray-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    {{ strtoupper(substr($ticket->targetDepartment->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $ticket->targetDepartment->name }}</h3>
                        @php
                            $statusClasses = [
                                'Pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'Open' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'In Progress' => 'bg-purple-50 text-purple-800 border-purple-200',
                                'Resolved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            ];
                        @endphp
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded border {{ $statusClasses[$ticket->status] ?? 'bg-slate-100' }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 truncate max-w-[200px]" title="{{ $ticket->title }}">
                        Ticket #{{ $ticket->id }} &bull; {{ $ticket->title }}
                    </p>
                </div>
            </div>

            <!-- Header Action Controls: Status & Phone Call Button -->
            <div class="flex items-center gap-2">
                <select wire:change="updateStatus($event.target.value)" 
                    class="text-xs font-medium px-2 py-1.5 rounded-lg bg-white border border-gray-300 text-slate-700 shadow-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    <option value="Pending" @selected($ticket->status === 'Pending')>Pending</option>
                    <option value="Open" @selected($ticket->status === 'Open')>Open</option>
                    <option value="In Progress" @selected($ticket->status === 'In Progress')>In Progress</option>
                    <option value="Resolved" @selected($ticket->status === 'Resolved')>Resolved</option>
                </select>

                <!-- Professional Phone / Call Icon Button -->
                <button wire:click="startCall" title="Establish Direct Audio Communication"
                    class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition cursor-pointer flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Simulated Active Voice Call Banner -->
        @if ($isCalling)
            <div class="p-3 bg-blue-900 text-white flex items-center justify-between shadow-inner transition">
                <div class="flex items-center gap-3">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                    <div>
                        <p class="text-xs font-bold leading-tight">VoIP Channel Connected</p>
                        <p class="text-[11px] text-blue-200">{{ $ticket->targetDepartment->name }} Specialist line &bull; Encrypted</p>
                    </div>
                </div>
                <button wire:click="endCall" class="px-2.5 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs">
                    Disconnect
                </button>
            </div>
        @endif

        <!-- Ticket Summary Quick Strip -->
        <div class="px-4 py-2 bg-slate-100/70 border-b border-gray-200 flex items-center justify-between text-xs text-slate-600">
            <span>Priority: <strong class="text-slate-900">{{ $ticket->priority }}</strong></span>
            <span>Category: <strong class="text-slate-900">{{ $ticket->category }}</strong></span>
            <span>Originator: <strong class="text-slate-900">{{ $ticket->sender->name }}</strong></span>
        </div>

        <!-- Chat Area (Scrollable area) -->
        <div id="chat-box" class="flex-1 p-4 overflow-y-auto space-y-3.5 bg-slate-50/50 min-h-[360px] max-h-[500px]">
            <!-- Original Description Box -->
            <div class="p-3 rounded-lg bg-white border border-gray-200 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Issue Summary</span>
                <p class="text-xs text-slate-700 leading-relaxed">{{ $ticket->description }}</p>
            </div>

            @forelse ($ticket->messages as $msg)
                @php
                    $isMe = $msg->user_id === $activeUserId;
                @endphp
                <div class="flex items-end gap-2.5 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    @if (! $isMe)
                        <img src="{{ $msg->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) }}" 
                             alt="{{ $msg->user->name }}" 
                             onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($msg->user->name) }}&background=0284c7&color=fff';"
                             class="w-7 h-7 rounded-full border border-gray-200 object-cover shrink-0" />
                    @endif

                    <div class="max-w-[80%]">
                        <div class="flex items-center gap-2 mb-1 px-0.5 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <span class="text-[11px] font-semibold text-slate-600">{{ $isMe ? 'You (' . $msg->user->name . ')' : $msg->user->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $msg->created_at->format('H:i') }}</span>
                        </div>

                        <!-- Sender: Light Blue bg-blue-100 with dark text; Receiver: Plain white with subtle border -->
                        <div class="p-3 rounded-xl text-xs leading-relaxed {{ $isMe ? 'bg-blue-100 text-slate-900 rounded-br-none shadow-xs' : 'bg-white border border-gray-200 text-slate-800 rounded-bl-none shadow-xs' }}">
                            {{ $msg->message }}
                        </div>
                    </div>

                    @if ($isMe)
                        <img src="{{ $msg->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) }}" 
                             alt="{{ $msg->user->name }}" 
                             onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($msg->user->name) }}&background=0284c7&color=fff';"
                             class="w-7 h-7 rounded-full border border-blue-200 object-cover shrink-0" />
                    @endif
                </div>
            @empty
                <div class="text-center py-10 text-slate-400">
                    <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-xs font-semibold text-slate-600">No communication logs recorded</p>
                    <p class="text-[11px] text-slate-400">Post an update below to initiate live coordination.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer: Text input and send button for auth users, login prompt for guests -->
        <div class="p-3.5 border-t border-gray-200 bg-white">
            @auth
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input type="text" wire:model="newMessage" placeholder="Type message to {{ $ticket->targetDepartment->name }}..."
                        class="flex-1 px-3.5 py-2.5 text-xs md:text-sm text-slate-800 bg-slate-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs" />
                    <button type="submit" 
                        class="px-4 py-2.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Send</span>
                    </button>
                </form>
                <div class="flex items-center justify-between mt-1.5 px-0.5 text-[10px] text-slate-400">
                    <span>Direct secure channel &bull; Auto-syncing</span>
                    <span>Press Enter to dispatch</span>
                </div>
            @else
                <div class="p-3 rounded-lg bg-slate-50 border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Masuk (Login) untuk membalas percakapan di tiket ini.</span>
                    </div>
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-md text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs text-center">
                        Masuk ke Akun
                    </a>
                </div>
            @endauth
        </div>
    @else
        <div class="flex flex-col items-center justify-center h-full p-8 text-center min-h-[420px]">
            <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Belum Ada Tiket yang Dipilih</h4>
            <p class="text-xs text-slate-500 max-w-xs mt-1">Pilih salah satu tiket dari antrean di bawah atau buat tiket baru melalui formulir di sebelah kiri untuk membuka ruang koordinasi inter-divisi.</p>
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
