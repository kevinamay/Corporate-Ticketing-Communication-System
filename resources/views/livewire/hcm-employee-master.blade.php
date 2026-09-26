<div class="space-y-6">
    <!-- Top Corporate Security Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl border border-blue-700/50 relative overflow-hidden">
        <!-- Background Decorative Pattern -->
        <div class="absolute right-0 top-0 -mt-6 -mr-6 w-56 h-56 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-500/30 text-blue-200 border border-blue-400/30">
                        <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        {{ __('Manajemen Data Karyawan') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ __('Admin IT Only') }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    {{ __('Daftar Akun Karyawan Terdaftar') }}
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/90 max-w-2xl leading-relaxed">
                    {{ __('Memuat seluruh data akun karyawan yang terdaftar di sistem. Terdiri dari Nama Lengkap, Nomor HP/WhatsApp, Email Aktif, Divisi, dan Waktu Registrasi.') }}
                </p>
            </div>

            <!-- Top Action Buttons: Tambah Manual (Blue), Import CSV (Emerald), Download Template (Slate) -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0">
                <!-- 1. Tambah Manual Button (Blue: bg-blue-600) -->
                <button type="button" 
                        wire:click="openCreateModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-blue-900/30 border border-blue-400/30 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('+ Tambah Karyawan') }}</span>
                </button>

                <!-- 2. Import CSV Button (Emerald: bg-emerald-600) -->
                <button type="button" 
                        wire:click="openCsvModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/30 border border-emerald-400/30 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>{{ __('Import CSV') }}</span>
                </button>

                <!-- 3. Download Format Template CSV -->
                <button type="button" 
                        wire:click="downloadTemplateCsv"
                        title="{{ __('Unduh contoh format CSV untuk pengisian data karyawan') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-white font-bold text-xs border border-white/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>{{ __('Template CSV') }}</span>
                </button>
            </div>
        </div>

        <!-- Metric Counters Ribbon -->
        <div class="mt-6 pt-5 border-t border-blue-700/50 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Total Akun Terdaftar:') }}</span>
                <span class="text-lg font-black text-white">{{ $totalEmployees }} {{ __('Pengguna') }}</span>
            </div>
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Status Verifikasi:') }}</span>
                <span class="text-lg font-black text-emerald-300">100% {{ __('Sudah Registrasi') }}</span>
            </div>
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Email Aktif & Terverifikasi:') }}</span>
                <span class="text-lg font-black text-emerald-300">{{ $registeredCount }} {{ __('Akun') }}</span>
            </div>
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Otoritas Akses:') }}</span>
                <span class="text-xs font-bold text-white uppercase">{{ auth()->user()->name }} ({{ auth()->user()->department?->name ?? 'Admin IT' }})</span>
            </div>
        </div>
    </div>

    <!-- Flash Alert Messages -->
    @if (session()->has('success_message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-xs animate-fade-in">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success_message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if (session()->has('error_message'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center justify-between shadow-xs animate-fade-in">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error_message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <!-- Main Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-md border border-gray-200 dark:border-slate-800 p-5 sm:p-6 transition-colors">
        
        <!-- Search and Filter Bar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
            <!-- Search Input -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="{{ __('Cari nama, no hp, email, atau divisi...') }}"
                       class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Department Filter Dropdown -->
            <div class="w-full sm:w-64">
                <select wire:model.live="departmentFilter"
                        class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="all">{{ __('Semua Divisi') }}</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-gray-100 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 w-12">{{ __('No') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Nama Lengkap') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('No. HP / WhatsApp') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Email Aktif') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Divisi') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Waktu Mendaftar') }}</th>
                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-300">
                    @forelse ($employees as $index => $emp)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <!-- No -->
                            <td class="py-3 px-4 font-mono text-slate-400 dark:text-slate-500">
                                {{ $employees->firstItem() + $index }}
                            </td>

                            <!-- Nama Lengkap -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $emp->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($emp->name).'&background=0284c7&color=fff' }}" 
                                         alt="{{ $emp->name }}" 
                                         class="w-7 h-7 rounded-full object-cover shrink-0 border border-gray-200 dark:border-slate-700">
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ $emp->name }}</p>
                                        <span class="text-[10px] uppercase font-semibold text-slate-400">{{ $emp->role ?? 'staff' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- No HP -->
                            <td class="py-3 px-4">
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $emp->whatsapp_number ?: '-' }}
                                </span>
                            </td>

                            <!-- Email Aktif -->
                            <td class="py-3 px-4">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $emp->email }}</span>
                                    @if ($emp->email_verified_at)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            {{ __('Aktif') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Divisi -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    {{ $emp->department->name ?? 'Belum Ditentukan' }}
                                </span>
                            </td>

                            <!-- Timestamp Mendaftar -->
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400 text-[11px] font-mono whitespace-nowrap">
                                {{ $emp->created_at ? $emp->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <!-- Actions (Edit & Delete) -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            wire:click="openEditModal({{ $emp->id }})"
                                            title="{{ __('Edit Data Karyawan') }}"
                                            class="p-1.5 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/60 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            wire:click="confirmDelete({{ $emp->id }})"
                                            title="{{ __('Hapus Karyawan') }}"
                                            class="p-1.5 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">{{ __('Belum ada data karyawan.') }}</p>
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto mb-4">{{ __('Gunakan tombol "Tambah Manual" atau "Import CSV" untuk mengisi data karyawan.') }}</p>
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" wire:click="openCreateModal" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs cursor-pointer">{{ __('Tambah Manual') }}</button>
                                    <button type="button" wire:click="openCsvModal" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs cursor-pointer">{{ __('Import CSV') }}</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 1: TAMBAH / EDIT KARYAWAN SECARA MANUAL -->
    <!-- ======================================================== -->
    @if ($isFormModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
             x-data
             x-init="$el.focus()"
             @keydown.escape.window="$wire.closeFormModal()">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs transition-opacity"
                 wire:click="closeFormModal"></div>

            <!-- Modal Content Card -->
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 w-full max-w-lg z-10 animate-fade-in text-slate-800 dark:text-slate-100 overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-800/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $editingEmployeeId ? __('Edit Data Akun Karyawan') : __('Tambah Akun Karyawan Baru') }}
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                {{ __('Input Nama, No HP, Email aktif, dan Divisi karyawan.') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeFormModal" 
                            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="saveEmployee" class="p-6 space-y-4 text-xs">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="form_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Nama Lengkap') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                                id="form_name" 
                                wire:model="name" 
                                placeholder="{{ __('Contoh: Budi Santoso') }}"
                                class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('name') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        @error('name') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- No HP / WhatsApp -->
                    <div>
                        <label for="form_whatsapp" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Nomor HP / WhatsApp Aktif') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                                id="form_whatsapp" 
                                wire:model="whatsapp_number" 
                                placeholder="{{ __('Contoh: 081234567890') }}"
                                class="w-full text-xs font-mono px-3.5 py-2.5 rounded-lg border @error('whatsapp_number') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        @error('whatsapp_number') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email Aktif -->
                    <div>
                        <label for="form_email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Alamat Email Aktif') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                                id="form_email" 
                                wire:model="email" 
                                placeholder="{{ __('Contoh: budi.santoso@asiaplastik.com') }}"
                                class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('email') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        @error('email') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Departemen / Divisi -->
                    <div>
                        <label for="form_dept" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Divisi / Departemen') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="form_dept" 
                                wire:model="department_id"
                                class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('department_id') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                            <option value="">{{ __('-- Pilih Divisi --') }}</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" 
                                wire:click="closeFormModal" 
                                class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                            {{ __('Batal') }}
                        </button>

                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="saveEmployee">{{ $editingEmployeeId ? __('Simpan Perubahan') : __('Simpan Akun') }}</span>
                            <span wire:loading wire:target="saveEmployee">{{ __('Menyimpan...') }}</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 2: KONFIRMASI HAPUS KARYAWAN -->
    <!-- ======================================================== -->
    @if ($isDeleteModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
             x-data
             x-init="$el.focus()"
             @keydown.escape.window="$wire.closeDeleteModal()">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs transition-opacity"
                 wire:click="closeDeleteModal"></div>

            <!-- Modal Content Card -->
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 w-full max-w-md z-10 animate-fade-in text-slate-800 dark:text-slate-100 overflow-hidden p-6 text-center">
                
                <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>

                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ __('Konfirmasi Hapus Akun Karyawan') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                    {{ __('Apakah Anda yakin ingin menghapus akun') }} <strong class="text-slate-800 dark:text-slate-200">{{ $deletingEmployeeName }}</strong>? {{ __('Tindakan ini tidak dapat dibatalkan.') }}
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button type="button" 
                            wire:click="closeDeleteModal" 
                            class="w-1/2 py-2.5 px-3 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 transition cursor-pointer">
                        {{ __('Batal') }}
                    </button>

                    <button type="button" 
                            wire:click="deleteEmployee" 
                            wire:loading.attr="disabled"
                            class="w-1/2 py-2.5 px-3 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md transition cursor-pointer">
                        <span wire:loading.remove wire:target="deleteEmployee">{{ __('Ya, Hapus') }}</span>
                        <span wire:loading wire:target="deleteEmployee">{{ __('Menghapus...') }}</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 3: IMPORT BULK CSV KARYAWAN -->
    <!-- ======================================================== -->
    @if ($isCsvModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
             x-data
             x-init="$el.focus()"
             @keydown.escape.window="$wire.closeCsvModal()">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs transition-opacity"
                 wire:click="closeCsvModal"></div>

            <!-- Modal Content Card -->
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 w-full max-w-lg z-10 animate-fade-in text-slate-800 dark:text-slate-100 overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-emerald-50/60 dark:bg-emerald-950/30">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ __('Import Massal Data Karyawan (CSV)') }}
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                {{ __('Unggah file CSV dengan kolom: name, whatsapp_number, email, department_id.') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeCsvModal" 
                            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="importCsv" class="p-6 space-y-4 text-xs">
                    
                    @if ($csvSuccessMessage)
                        <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs">
                            {{ $csvSuccessMessage }}
                        </div>
                    @endif

                    @if ($csvErrorMessage)
                        <div class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs">
                            {{ $csvErrorMessage }}
                        </div>
                    @endif

                    <!-- File Input Box -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Pilih File CSV (.csv / .txt)') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-xl p-4 text-center hover:border-emerald-500 dark:hover:border-emerald-500 transition">
                            <input type="file" 
                                   wire:model="csvFile" 
                                   accept=".csv,.txt"
                                   class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/60 dark:file:text-emerald-300 cursor-pointer">
                        </div>
                        @error('csvFile') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Guide Info -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-800 text-[11px] space-y-1">
                        <p class="font-bold text-slate-700 dark:text-slate-200">{{ __('Panduan Format CSV:') }}</p>
                        <p class="text-slate-500 dark:text-slate-400">{{ __('1. Header wajib: name, whatsapp_number, email, department_id.') }}</p>
                        <p class="text-slate-500 dark:text-slate-400">{{ __('2. Password awal otomatis diset ke "password123".') }}</p>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                        <button type="button" 
                                wire:click="downloadTemplateCsv"
                                class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold text-xs flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>{{ __('Unduh Contoh CSV') }}</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    wire:click="closeCsvModal" 
                                    class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 transition cursor-pointer">
                                {{ __('Tutup') }}
                            </button>

                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer">
                                <span wire:loading.remove wire:target="importCsv">{{ __('Proses Import') }}</span>
                                <span wire:loading wire:target="importCsv">{{ __('Mengunggah...') }}</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
