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
                        <option value="{{ $dept->id }}">{{ __($dept->name) }}</option>
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
                        <option value="{{ $dept->id }}">{{ __($dept->name) }}</option>
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

        <!-- Rich Editor: Problem Details / Description -->
        <div x-data="ticketRichEditor()" x-init="init()" class="space-y-1.5" @click.outside="closeMenus()">
            <div class="flex items-center justify-between">
                <label for="ticket_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    {{ __('Problem Details / Description') }}
                </label>
                <!-- Quick Mode Toggle (Edit / Preview) -->
                <button type="button" @click="togglePreview()" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-medium text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                    <template x-if="!previewMode">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ __('Preview') }}
                        </span>
                    </template>
                    <template x-if="previewMode">
                        <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            {{ __('Edit') }}
                        </span>
                    </template>
                </button>
            </div>

            <!-- Editor Outer Card (Matching Jira toolbar & container design) -->
            <div class="relative rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-2xs transition focus-within:ring-2 focus-within:ring-blue-500/25 focus-within:border-blue-500 overflow-visible">
                
                <!-- TOOLBAR (Matches image 1 & 3) -->
                <div class="px-2 py-1.5 border-b border-gray-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-800/50 rounded-t-xl flex flex-wrap items-center gap-0.5 sm:gap-1 text-slate-700 dark:text-slate-200 relative select-none">
                    
                    <!-- 1. AI Logo & Dropdown -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('ai')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5"
                            title="{{ __('AI Smart Formatting') }}">
                            <!-- Colorful Gemini Sparkle Icon -->
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2C12 7.52 7.52 12 2 12C7.52 12 12 16.48 12 22C12 16.48 16.48 12 22 12C16.48 12 12 7.52 12 2Z" fill="url(#aiGradient)" />
                                <defs>
                                    <linearGradient id="aiGradient" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#3b82f6" />
                                        <stop offset="0.33" stop-color="#8b5cf6" />
                                        <stop offset="0.66" stop-color="#ec4899" />
                                        <stop offset="1" stop-color="#f59e0b" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- AI Dropdown Menu -->
                        <div x-show="openMenu === 'ai'" x-cloak 
                            class="absolute left-0 mt-1.5 w-64 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1.5 z-50 text-xs">
                            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-gray-100 dark:border-slate-700/60 mb-1">
                                {{ __('AI Smart Formatting') }}
                            </div>
                            <button type="button" @click="improveDescription('smart_polish')" class="w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-slate-700 flex items-start gap-2.5 text-slate-700 dark:text-slate-200 transition cursor-pointer">
                                <span class="text-blue-500 font-bold">✨</span>
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ __('Auto-structure into Incident Report') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ __('Format teks menjadi laporan standar: Gejala, Langkah, & Dampak') }}</div>
                                </div>
                            </button>
                            <button type="button" @click="improveDescription('bullets')" class="w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-slate-700 flex items-start gap-2.5 text-slate-700 dark:text-slate-200 transition cursor-pointer">
                                <span class="text-amber-500 font-bold">📋</span>
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ __('Format as Bullet Points') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ __('Ubah baris menjadi daftar poin teratur') }}</div>
                                </div>
                            </button>
                            <button type="button" @click="improveDescription('clean')" class="w-full text-left px-3 py-2 hover:bg-rose-50 dark:hover:bg-slate-700 flex items-start gap-2.5 text-slate-700 dark:text-slate-200 transition border-t border-gray-100 dark:border-slate-700/60 cursor-pointer">
                                <span class="text-slate-400 font-bold">🧹</span>
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ __('Clean Formatting') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ __('Kembalikan ke teks polos tanpa markdown') }}</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 2. "Improve description" Wand Button -->
                    <button type="button" @click="improveDescription('smart_polish')" 
                        class="px-2 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer flex items-center gap-1.5 text-xs font-medium border border-transparent hover:border-blue-200 dark:hover:border-blue-800"
                        title="{{ __('Improve description') }}">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span>{{ __('Improve description') }}</span>
                    </button>

                    <!-- Divider -->
                    <span class="h-4 w-px bg-gray-200 dark:bg-slate-700 mx-0.5 sm:mx-1"></span>

                    <!-- 3. "T" (Heading / Text Style) -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('heading')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5 font-bold text-xs"
                            title="{{ __('Heading 1 (Main Title)') }}">
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

                    <!-- 4. "B" (Bold & Typography Dropdown) -->
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

                    <!-- 5. List Dropdown -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('list')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5"
                            title="{{ __('Bulleted List') }}">
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

                    <!-- 6. [A] Text Color & Badges -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('color')" 
                            class="p-1 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer flex items-center gap-0.5"
                            title="{{ __('Text Accent & Badges') }}">
                            <span class="w-5 h-5 flex items-center justify-center border border-gray-300 dark:border-slate-600 rounded font-bold text-xs">A</span>
                        </button>
                        <div x-show="openMenu === 'color'" x-cloak class="absolute left-0 mt-1.5 w-48 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Text Accent & Badges') }}</div>
                            <button type="button" @click="applyColor('URGENT')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-rose-600 dark:text-rose-400 font-semibold cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <span>🔴 {{ __('Urgent / Critical') }}</span>
                            </button>
                            <button type="button" @click="applyColor('WARNING')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-amber-600 dark:text-amber-400 font-semibold cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span>🟠 {{ __('Warning') }}</span>
                            </button>
                            <button type="button" @click="applyColor('INFO')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                <span>🔵 {{ __('Important Info') }}</span>
                            </button>
                            <button type="button" @click="applyColor('SOLVED')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>🟢 {{ __('Success / Resolved') }}</span>
                            </button>
                            <button type="button" @click="wrapSelection('<mark>', '</mark>', 'teks sorotan')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 text-slate-800 dark:text-slate-200 border-t border-gray-100 dark:border-slate-700/60 mt-0.5 cursor-pointer">
                                <span class="px-1 bg-yellow-200 dark:bg-yellow-800 text-[10px] rounded font-bold">ABC</span>
                                <span>{{ __('Highlight Text') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- 7. Image Icon (Attach photo) -->
                    <button type="button" @click="triggerPhotoUpload()" 
                        class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                        title="{{ __('Attach Photo / Screenshot') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15l-5-5L5 21"/>
                        </svg>
                    </button>

                    <!-- 8. Code Icon (</>) -->
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

                    <!-- 9. Emoji Icon (😊) -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('emoji')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer text-xs"
                            title="{{ __('Insert Emoji') }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>
                        </button>
                        <div x-show="openMenu === 'emoji'" x-cloak class="absolute left-0 mt-1.5 w-60 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl p-2 z-50">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ __('Insert Emoji') }}</div>
                            <div class="grid grid-cols-7 gap-1 text-base text-center">
                                <template x-for="emo in ['⚠️', '❌', '❗', '🚨', '🕒', '❓', '✅', '💡', '💻', '🖥️', '🖨️', '🔌', '🌐', '📡', '⚙️', '🔧', '📄', '📌', '📝', '🏢', '📞']">
                                    <button type="button" @click="insertText(emo)" class="w-7 h-7 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center cursor-pointer text-sm" x-text="emo"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 10. "+" Insert More -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('insert')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer font-bold text-xs"
                            title="{{ __('Insert More') }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        <div x-show="openMenu === 'insert'" x-cloak class="absolute left-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                            <button type="button" @click="insertAtLineStart('> ')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span class="font-serif italic font-bold">“</span>
                                <span>{{ __('Insert Quote') }}</span>
                            </button>
                            <button type="button" @click="insertText('\n---\n')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span>―</span>
                                <span>{{ __('Insert Divider') }}</span>
                            </button>
                            <button type="button" @click="insertText('\n| Item | Keterangan | Status |\n| --- | --- | --- |\n| Contoh 1 | Deskripsi | Normal |\n')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
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

                    <!-- 12. Undo (↺) -->
                    <button type="button" @click="undo()" :disabled="historyIndex <= 0" 
                        class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                        title="{{ __('Undo') }} (Ctrl+Z)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"/></svg>
                    </button>

                    <!-- 13. Redo (↻) -->
                    <button type="button" @click="redo()" :disabled="historyIndex >= history.length - 1" 
                        class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                        title="{{ __('Redo') }} (Ctrl+Y)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"/></svg>
                    </button>

                    <!-- 14. History / Incident Templates (🕒) -->
                    <div class="relative inline-block">
                        <button type="button" @click.stop="toggleMenu('template')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                            title="{{ __('Incident Templates') }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 7v5l3 2"/></svg>
                        </button>
                        <div x-show="openMenu === 'template'" x-cloak class="absolute right-0 sm:left-0 mt-1.5 w-60 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1.5 z-50 text-xs">
                            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Incident Templates') }}</div>
                            <button type="button" @click="insertTemplate('pc')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span>🖥️</span>
                                <div>
                                    <div class="font-semibold">{{ __('PC / Laptop Issue') }}</div>
                                    <div class="text-[10px] text-slate-400">Restart, layar biru, mati total</div>
                                </div>
                            </button>
                            <button type="button" @click="insertTemplate('network')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span>🌐</span>
                                <div>
                                    <div class="font-semibold">{{ __('Network & Wi-Fi Issue') }}</div>
                                    <div class="text-[10px] text-slate-400">Wi-Fi lambat, putus, LAN</div>
                                </div>
                            </button>
                            <button type="button" @click="insertTemplate('printer')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span>🖨️</span>
                                <div>
                                    <div class="font-semibold">{{ __('Printer / Scanner Issue') }}</div>
                                    <div class="text-[10px] text-slate-400">Kertas macet, tinta, driver</div>
                                </div>
                            </button>
                            <button type="button" @click="insertTemplate('account')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span>🔑</span>
                                <div>
                                    <div class="font-semibold">{{ __('Account & Password Reset') }}</div>
                                    <div class="text-[10px] text-slate-400">Lupa password, email, ERP</div>
                                </div>
                            </button>
                            <button type="button" @click="insertTemplate('maintenance')" class="w-full text-left px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                <span>❄️</span>
                                <div>
                                    <div class="font-semibold">{{ __('Facility & Maintenance') }}</div>
                                    <div class="text-[10px] text-slate-400">AC tidak dingin, lampu, pintu</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 15. More Options (...) -->
                    <div class="relative inline-block ml-auto sm:ml-0">
                        <button type="button" @click.stop="toggleMenu('more')" 
                            class="p-1.5 rounded-lg hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer"
                            title="{{ __('More Options') }}">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                        </button>
                        <div x-show="openMenu === 'more'" x-cloak class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-xl py-1 z-50 text-xs">
                            <button type="button" @click="togglePreview()" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-between cursor-pointer">
                                <span x-text="previewMode ? '{{ __('Edit Mode') }}' : '{{ __('Preview Mode') }}'"></span>
                                <span class="text-[10px] text-slate-400">👁️</span>
                            </button>
                            <button type="button" @click="improveDescription('clean')" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-between cursor-pointer">
                                <span>{{ __('Clean Formatting') }}</span>
                                <span class="text-[10px] text-slate-400">🧹</span>
                            </button>
                            <button type="button" @click="clearContent()" class="w-full text-left px-3 py-1.5 hover:bg-rose-50 dark:hover:bg-slate-700 text-rose-600 dark:text-rose-400 border-t border-gray-100 dark:border-slate-700/60 mt-0.5 flex items-center justify-between cursor-pointer">
                                <span>{{ __('Kosongkan Deskripsi') }}</span>
                                <span class="text-[10px]">🗑️</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- EDIT / TEXTAREA VIEW -->
                <div x-show="!previewMode" class="relative">
                    <textarea id="ticket_description" 
                        wire:model="description" 
                        x-ref="textarea"
                        @input="onInput()" 
                        @keydown="onKeydown($event)" 
                        rows="5" 
                        placeholder="{{ __('Detail the situation, asset tag, affected personnel, error messages, and steps already attempted...') }}"
                        class="w-full px-3.5 py-3 text-sm text-slate-800 dark:text-slate-100 bg-transparent border-0 focus:outline-none focus:ring-0 resize-y min-h-[130px] placeholder:text-slate-400 dark:placeholder:text-slate-500 leading-relaxed font-sans"></textarea>
                </div>

                <!-- PREVIEW MODE VIEW -->
                <div x-show="previewMode" x-cloak class="p-3.5 min-h-[130px] max-h-[300px] overflow-y-auto bg-slate-50/50 dark:bg-slate-900/60 rounded-b-xl border-t border-dashed border-gray-200 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-gray-200 dark:border-slate-800 text-[11px] text-slate-400">
                        <span class="font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ __('Preview') }}
                        </span>
                        <button type="button" @click="togglePreview()" class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                            &larr; {{ __('Kembali ke Edit') }}
                        </button>
                    </div>
                    <div class="prose-ticket text-xs md:text-sm text-slate-800 dark:text-slate-100" x-html="renderMarkdown($refs.textarea ? $refs.textarea.value : '')"></div>
                </div>

                <!-- BOTTOM STATUS BAR -->
                <div class="px-3 py-1.5 bg-slate-50/60 dark:bg-slate-850/60 border-t border-gray-200/80 dark:border-slate-800/80 rounded-b-xl flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
                    <div class="flex items-center gap-2">
                        <span :class="charCount >= 10 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-amber-600 dark:text-amber-400'">
                            <span x-text="charCount">0</span> {{ __('characters') }}
                            <template x-if="charCount >= 10">
                                <span class="ml-0.5">✓</span>
                            </template>
                        </span>
                        <span>•</span>
                        <span><span x-text="wordCount">0</span> {{ __('words') }}</span>
                        <template x-if="charCount < 10">
                            <span class="text-[10px] text-slate-400 italic">({{ __('Min. 10 karakter') }})</span>
                        </template>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 text-[10px]">
                        <span class="text-slate-400">Ctrl+B: {{ __('Bold') }}</span>
                        <span>•</span>
                        <span class="text-slate-400">Ctrl+Z: {{ __('Undo') }}</span>
                    </div>
                </div>

            </div>
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
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ticketRichEditor', () => ticketRichEditorImpl());
    });

    if (window.Alpine) {
        Alpine.data('ticketRichEditor', () => ticketRichEditorImpl());
    }

    function ticketRichEditor() {
        return ticketRichEditorImpl();
    }

    function ticketRichEditorImpl() {
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

                this.$watch('$wire.description', (val) => {
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
                    this.$wire.set('description', val);
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
                    this.wrapSelection('`', '`', 'kode_error');
                }
            },

            insertLink() {
                const url = prompt('Masukkan URL tautan:', 'https://');
                if (url) {
                    this.wrapSelection('[', `](${url})`, 'Teks Tautan');
                }
            },

            triggerPhotoUpload() {
                const photoInput = document.getElementById('ticket_photo');
                if (photoInput) {
                    photoInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    photoInput.focus();
                    photoInput.click();
                }
                this.closeMenus();
            },

            clearContent() {
                if (confirm('Apakah Anda yakin ingin mengosongkan teks deskripsi?')) {
                    this.setText('');
                }
                this.closeMenus();
            },

            insertTemplate(type) {
                let tpl = '';
                if (type === 'pc') {
                    tpl = "### 🖥️ Gangguan Komputer / PC / Laptop\n- **Nomor Aset / Hostname**: \n- **Gejala Kerusakan**: \n- **Pesan Error**: \n- **Langkah yang Sudah Dicoba**: Restart PC\n- **Lokasi & Kontak**: ";
                } else if (type === 'network') {
                    tpl = "### 🌐 Gangguan Jaringan & Internet\n- **Jenis Koneksi**: Wi-Fi / LAN Kabel\n- **Lokasi / Lantai**: \n- **Dampak**: Tidak bisa akses ERP / Internet\n- **Pesan Kesalahan**: Halaman timeout / Disconnected\n- **Waktu Mulai Terjadi**: ";
                } else if (type === 'printer') {
                    tpl = "### 🖨️ Kendala Printer / Scanner\n- **Nama / Model Printer**: \n- **Lokasi Printer**: \n- **Status Lampu Indikator**: \n- **Kendala**: Kertas tersangkut / Tinta habis / Tidak terdeteksi";
                } else if (type === 'account') {
                    tpl = "### 🔑 Akses Akun & Reset Kata Sandi\n- **Nama Sistem**: ERP / Email Kantor / Portal Karyawan\n- **Username / NIK**: \n- **Permasalahan**: Lupa password / Akun terblokir\n- **Departemen**: ";
                } else if (type === 'maintenance') {
                    tpl = "### ❄️ Pemeliharaan Fasilitas & Ruangan\n- **Lokasi Ruangan / Lantai**: \n- **Fasilitas Terkait**: AC / Lampu / Pintu / Kelistrikan\n- **Uraian Kerusakan**: \n- **Tingkat Urgensi**: ";
                } else if (type === 'general') {
                    tpl = "### 📋 Laporan Masalah / Kendala\n- **Deskripsi Kejadian**: \n- **Dampak Operasional**: \n- **Tindakan Mandiri**: \n- **Catatan Tambahan**: ";
                }
                if (tpl) {
                    const el = this.$refs.textarea;
                    if (el && el.value.trim().length > 0) {
                        if (confirm('Tambahkan template ini ke dalam deskripsi?')) {
                            this.insertText('\n\n' + tpl);
                        }
                    } else {
                        this.setText(tpl);
                    }
                }
                this.closeMenus();
            },

            improveDescription(action = 'smart_polish') {
                const el = this.$refs.textarea;
                const current = el ? el.value.trim() : '';
                
                if (!current) {
                    this.insertTemplate('general');
                    this.closeMenus();
                    return;
                }

                if (action === 'smart_polish') {
                    const formatted = "### 📌 Ringkasan Masalah\n" + current + "\n\n### 🔍 Rincian Situasi & Gejala\n- Kendala muncul saat menjalankan proses pekerjaan\n- Pesan Kesalahan / Error: [Sebutkan jika ada]\n\n### 🛠️ Langkah yang Sudah Dicoba\n- [ ] Memuat ulang (refresh) halaman atau restart aplikasi\n- [ ] Memeriksa koneksi perangkat\n\n### ⚠️ Dampak Terhadap Pekerjaan\n- Memerlukan penanganan segera agar operasional kembali lancar";
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
                    return '<p class="text-slate-400 dark:text-slate-500 italic text-xs">Belum ada rincian yang dimasukkan...</p>';
                }
                let html = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
                
                // Headers
                html = html.replace(/^### (.*$)/gim, '<h3 class="font-bold text-sm text-slate-900 dark:text-white mt-2 mb-1">$1</h3>');
                html = html.replace(/^## (.*$)/gim, '<h2 class="font-bold text-base text-slate-900 dark:text-white mt-2.5 mb-1">$1</h2>');
                html = html.replace(/^# (.*$)/gim, '<h1 class="font-bold text-lg text-slate-900 dark:text-white mt-3 mb-1.5">$1</h1>');
                
                // Bold & Italic
                html = html.replace(/\*\*([^*]+)\*\*/gim, '<strong class="font-bold text-slate-900 dark:text-white">$1</strong>');
                html = html.replace(/\*([^*]+)\*/gim, '<em class="italic text-slate-800 dark:text-slate-200">$1</em>');
                
                // Inline code & code block
                html = html.replace(/```([\s\S]*?)```/gim, '<pre class="bg-slate-900 text-slate-100 p-2.5 rounded-lg text-xs font-mono my-2 overflow-x-auto"><code>$1</code></pre>');
                html = html.replace(/`([^`]+)`/gim, '<code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-sky-600 dark:text-sky-400 font-mono text-xs">$1</code>');
                
                // Badges
                html = html.replace(/\[URGENT\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">URGENT</span>');
                html = html.replace(/\[WARNING\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">WARNING</span>');
                html = html.replace(/\[INFO\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">INFO</span>');
                html = html.replace(/\[SOLVED\]/gim, '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">SOLVED</span>');
                
                // Checklist
                html = html.replace(/^- \[(x|X)\] (.*$)/gim, '<div class="flex items-center gap-1.5 text-xs text-slate-500 line-through my-0.5"><span class="text-emerald-500 font-bold">✓</span> $2</div>');
                html = html.replace(/^- \[ \] (.*$)/gim, '<div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 my-0.5"><span class="w-3.5 h-3.5 rounded border border-gray-400 dark:border-slate-600 inline-block"></span> $1</div>');
                
                // Bullets
                html = html.replace(/^- (.*$)/gim, '<li class="ml-4 list-disc text-xs text-slate-700 dark:text-slate-300 my-0.5">$1</li>');
                
                // Quotes
                html = html.replace(/^> (.*$)/gim, '<blockquote class="border-l-3 border-blue-500 pl-2.5 py-1 italic text-xs text-slate-600 dark:text-slate-400 my-1.5 bg-blue-50/50 dark:bg-blue-950/20 rounded-r">$1</blockquote>');
                
                // Line breaks
                html = html.replace(/\n/gim, '<br>');
                return html;
            }
        };
    }

    // Automated WhatsApp Bot Dispatcher (Sistem PATEN - Single Dispatch Guard)
    let lastDispatchedTicketId = null;
    let lastDispatchedTime = 0;

    function triggerWahaWhatsAppNotification(detail) {
        try {
            const data = (detail && detail.ticketData) ? detail.ticketData : (detail || {});
            if (!data || !data.id) return;

            // Anti-Double Prevention: Prevent sending the same ticket more than once
            const now = Date.now();
            if (lastDispatchedTicketId === data.id && (now - lastDispatchedTime) < 5000) {
                return;
            }
            lastDispatchedTicketId = data.id;
            lastDispatchedTime = now;

            const sender = data.sender || 'Karyawan';
            const department = data.department || 'Staff';
            const category = data.category || 'Umum';
            const priority = (data.priority || 'Normal').toUpperCase();
            const title = data.title || '-';
            const ticketId = data.id;

            const dateStr = new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
            const timeStr = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

            const message = `*TIKET BARU MASUK*\n━━━━━━━━━━━━━━━━━━━━\n*Pengirim:* ${sender}\n*Divisi:* ${department}\n*Kategori:* ${category}\n*Prioritas:* ${priority}\n*Masalah:* ${title}\n*Waktu:* ${dateStr}, ${timeStr} WIB\n━━━━━━━━━━━━━━━━━━━━\nSegera proses tiket ini dengan klik link berikut:\nhttps://ticketing-kappa-jet.vercel.app/dashboard?ticket=${ticketId}`;

            const adminPhone = (data.adminPhone || '6285784694910').replace(/[^0-9]/g, '').replace(/^0/, '62');

            fetch('http://localhost:3000/api/sendText', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Api-Key': 'e8928adf08ec4cfd8b30dea033ee38bc'
                },
                body: JSON.stringify({
                    chatId: adminPhone + '@c.us',
                    text: message,
                    session: 'default'
                })
            }).then(r => {
                if (r.ok) {
                    console.log('✅ WAHA Bot: Notifikasi tiket #' + ticketId + ' berhasil terkirim 1 kali ke Admin IT (' + adminPhone + ')');
                }
            }).catch(() => {
                // Fail silently if local gateway is offline
            });
        } catch (err) {}
    }

    // Single Event Listener (No Duplicate)
    window.addEventListener('ticketCreated', (e) => {
        triggerWahaWhatsAppNotification(e.detail);
    });
</script>
