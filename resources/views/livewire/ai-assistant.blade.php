<div x-data="{
        isOpen: @entangle('isOpen'),
        scrollToBottom() {
            this.$nextTick(() => {
                const box = this.$refs.chatMessages;
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
            });
        }
     }"
     x-init="
        $wire.on('chat-updated', () => scrollToBottom());
        $wire.on('chat-opened', () => scrollToBottom());
     "
     class="relative">

    <!-- STACKED FLOATING ACTION BUTTONS (WhatsApp on Top, AI Assistant Below) -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3 pointer-events-auto">

        <!-- 1. WHATSAPP BUTTON (Connected directly to +6231 8433078 / 8439998) -->
        <a href="https://wa.me/62318433078?text=Halo%20Admin%20PT.%20Asia%20Plastik%2C%20saya%20butuh%20bantuan%20terkait%20layanan%20dan%20sistem..." 
           target="_blank" 
           rel="noopener noreferrer"
           class="flex items-center shadow-2xl rounded-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden hover:scale-105 active:scale-95 transition-all duration-200 group cursor-pointer"
           title="{{ __('Hubungi WhatsApp PT. Asia Plastik (+6231 8433078 / 8439998)') }}">
            
            <span class="px-4 py-2.5 text-xs font-black text-slate-800 dark:text-slate-100 tracking-tight select-none flex items-center gap-1.5">
                <span>{{ __('WhatsApp') }}</span>
            </span>

            <span class="w-11 h-11 bg-[#25D366] group-hover:bg-[#20ba5a] transition-colors flex items-center justify-center text-white shadow-inner shrink-0">
                <!-- Official WhatsApp Vector Logo -->
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
            </span>
        </a>

        <!-- 2. AI ASSISTANT BUTTON (Opens interactive AI chatbot) -->
        <div class="relative flex items-center">
            <!-- Live Pulse Status Badge -->
            <span class="absolute -top-1 -left-1 flex h-3.5 w-3.5 z-10 pointer-events-none">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-blue-500 border-2 border-white dark:border-slate-900"></span>
            </span>

            <button type="button" 
                    @click="isOpen = !isOpen; if(isOpen) scrollToBottom();" 
                    class="flex items-center shadow-2xl rounded-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden hover:scale-105 active:scale-95 transition-all duration-200 group cursor-pointer"
                    title="{{ __('Buka AI Assistant') }}">
                
                <span class="px-4 py-2.5 text-xs font-black text-slate-800 dark:text-slate-100 tracking-tight select-none flex items-center gap-1.5">
                    <span x-show="!isOpen">{{ __('AI Assistant') }}</span>
                    <span x-show="isOpen" style="display: none;" class="text-blue-600 dark:text-blue-400 font-bold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        {{ __('Tutup AI') }}
                    </span>
                </span>

                <span class="w-11 h-11 bg-gradient-to-tr from-blue-600 to-indigo-600 group-hover:from-blue-700 group-hover:to-indigo-700 transition-colors flex items-center justify-center text-white shadow-inner shrink-0">
                    <template x-if="!isOpen">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </template>
                    <template x-if="isOpen">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </template>
                </span>
            </button>
        </div>

    </div>

    <!-- AI CHATBOT DIALOG WINDOW (Anchored cleanly above the stacked buttons) -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         @click.away="isOpen = false"
         class="fixed bottom-36 right-4 sm:right-6 z-50 w-[350px] sm:w-[410px] max-w-[calc(100vw-2rem)] h-[540px] max-h-[75vh] bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden text-slate-800 dark:text-slate-100 transition-colors"
         style="display: none;">

        <!-- Chat Header (Modern Corporate Blue & Emerald Gradient) -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-800 to-slate-900 px-4 py-3.5 text-white flex items-center justify-between shadow-md select-none shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-9 h-9 rounded-full bg-white/15 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner font-bold text-sm">
                        🤖
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-slate-900 rounded-full animate-pulse"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs sm:text-sm font-black tracking-tight text-white leading-tight">AsiaBot AI</h4>
                        <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.2 rounded-full bg-emerald-500/40 text-emerald-200 border border-emerald-400/30">24/7</span>
                    </div>
                    <p class="text-[10px] text-blue-200 leading-tight flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('Online • Asisten Tiket PT Asia Plastik') }}
                    </p>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center gap-1">
                <!-- Reset Chat Button -->
                <button type="button" 
                        wire:click="resetChat" 
                        title="{{ __('Reset Percakapan') }}"
                        class="p-1.5 rounded-lg hover:bg-white/15 text-blue-200 hover:text-white transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>

                <!-- Close Button -->
                <button type="button" 
                        @click="isOpen = false" 
                        title="{{ __('Tutup Chat') }}"
                        class="p-1.5 rounded-lg hover:bg-white/15 text-blue-200 hover:text-white transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div x-ref="chatMessages" 
             class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/80 dark:bg-slate-950/70 scroll-smooth">
            
            <!-- Company Mini Notice -->
            <div class="text-center my-1">
                <span class="inline-block px-3 py-1 rounded-full bg-white dark:bg-slate-800 text-[10px] font-medium text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800 shadow-2xs">
                    🔒 {{ __('Percakapan internal terlindungi enkripsi sistem') }}
                </span>
            </div>

            <!-- Loop Messages -->
            @foreach($messages as $msg)
                @if($msg['sender'] === 'bot')
                    <!-- Bot Message Item -->
                    <div class="flex items-start gap-2 max-w-[90%]">
                        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs mt-0.5">
                            🤖
                        </div>
                        <div>
                            <div class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-xs p-3 text-xs leading-relaxed shadow-xs border border-slate-200/70 dark:border-slate-700/60 whitespace-pre-line">
                                {!! nl2br(e($msg['text'])) !!}
                            </div>
                            <span class="block text-[9px] text-slate-400 dark:text-slate-500 mt-1 ml-1 font-medium">{{ $msg['time'] }}</span>
                        </div>
                    </div>
                @else
                    <!-- User Message Item -->
                    <div class="flex flex-col items-end max-w-[85%] ml-auto">
                        <div class="bg-blue-600 dark:bg-blue-700 text-white rounded-2xl rounded-tr-xs p-3 text-xs leading-relaxed shadow-sm whitespace-pre-line">
                            {{ $msg['text'] }}
                        </div>
                        <span class="block text-[9px] text-slate-400 dark:text-slate-500 mt-1 mr-1 font-medium">{{ $msg['time'] }}</span>
                    </div>
                @endif
            @endforeach

            <!-- AI Typing Indicator -->
            <div wire:loading wire:target="sendMessage,sendSuggestion" class="flex items-start gap-2 max-w-[80%]">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs mt-0.5">
                    🤖
                </div>
                <div class="bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-2xl rounded-tl-xs px-3.5 py-2.5 text-xs shadow-xs border border-slate-200/70 dark:border-slate-700/60 flex items-center gap-2">
                    <span class="flex gap-1 items-center">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce"></span>
                    </span>
                    <span class="text-[11px] font-medium">{{ __('AsiaBot sedang memproses...') }}</span>
                </div>
            </div>

            <!-- Quick Suggestion Chips (Prompt Pills) -->
            <div class="pt-2">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 ml-1">
                    {{ __('Saran Pertanyaan Cepat:') }}
                </p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($suggestions as $suggestion)
                        <button type="button" 
                                wire:click="sendSuggestion('{{ addslashes($suggestion) }}')" 
                                class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 shadow-2xs transition-all active:scale-95 cursor-pointer">
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Chat Input Footer -->
        <div class="p-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <input type="text" 
                       wire:model="userInput" 
                       placeholder="{{ __('Tanyakan tiket atau kendala pabrik...') }}" 
                       autocomplete="off"
                       class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="sendMessage,sendSuggestion"
                        class="w-9 h-9 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl flex items-center justify-center transition shadow-md active:scale-95 cursor-pointer shrink-0"
                        title="{{ __('Kirim Pesan') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-2 flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 px-1">
                <span>⚡ {{ __('Didukung AsiaBot AI • PT. Asia Plastik') }}</span>
                <span>{{ __('Tekan Enter ↵') }}</span>
            </div>
        </div>

    </div>
</div>
