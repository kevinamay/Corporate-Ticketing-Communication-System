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
                        {{ __('HCM Core Security Vault') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ __('HR Department Only') }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    {{ __('HCM Employee Master Data') }}
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/90 max-w-2xl leading-relaxed">
                    {{ __('Pusat otorisasi identitas resmi NIK/KTP karyawan PT Asia Plastik. Data di sini menjadi gerbang tunggal (Gatekeeper) validasi pendaftaran akun karyawan baru di seluruh divisi.') }}
                </p>
            </div>

            <!-- Top Action Buttons: Tambah Manual (Blue) & Import CSV (Green/Gray) -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 shrink-0">
                <!-- 1. Tambah Manual Button (Blue: bg-blue-600) -->
                <button type="button" 
                        wire:click="openCreateModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-blue-900/30 border border-blue-400/30 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('Tambah Manual') }}</span>
                </button>

                <!-- 2. Import CSV Button (Green/Gray: bg-emerald-600) -->
                <button type="button" 
                        wire:click="openCsvModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-950/30 border border-emerald-400/30 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>{{ __('Import CSV') }}</span>
                </button>

                <!-- Back to Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>
            </div>
        </div>

        <!-- Metric Counters Ribbon -->
        <div class="mt-6 pt-5 border-t border-blue-700/50 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Total Karyawan Terdaftar:') }}</span>
                <span class="text-lg font-black text-white">{{ $totalEmployees }} {{ __('Jiwa') }}</span>
            </div>
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Akun Sistem Aktif:') }}</span>
                <span class="text-lg font-black text-emerald-300">{{ $registeredCount }} {{ __('Pengguna') }}</span>
            </div>
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Belum Registrasi Portal:') }}</span>
                <span class="text-lg font-black text-amber-300">{{ max(0, $totalEmployees - $registeredCount) }} {{ __('Karyawan') }}</span>
            </div>
            <div>
                <span class="text-blue-200/80 block text-[11px]">{{ __('Otoritas Akses:') }}</span>
                <span class="text-xs font-bold text-white uppercase">{{ auth()->user()->name }} ({{ auth()->user()->department?->name ?? 'HRD' }})</span>
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

    <!-- Main Data Table Container Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-md p-5 sm:p-6 transition-colors">
        
        <!-- Search & Filter Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 pb-5 border-b border-gray-100 dark:border-slate-800">
            <!-- Search Bar -->
            <div class="w-full sm:w-96 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="{{ __('Cari NIK/KTP, nama karyawan, atau divisi...') }}"
                       class="w-full pl-9 pr-4 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Department Filter -->
            <div class="w-full sm:w-64">
                <select wire:model.live="departmentFilter" 
                        class="w-full px-3 py-2 text-xs text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800/90 rounded-xl border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-2xs">
                    <option value="all">{{ __('Semua Departemen Penugasan') }}</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Employees Data Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                    <tr>
                        <th scope="col" class="py-3.5 px-4">{{ __('No') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Nomor KTP (NIK)') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Nama Karyawan') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Departemen') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Status Akun Portal') }}</th>
                        <th scope="col" class="py-3.5 px-4">{{ __('Terdaftar Sejak') }}</th>
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

                            <!-- KTP / NIK Number -->
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-blue-900 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded border border-blue-200/60 dark:border-blue-900/60">
                                    {{ $emp->ktp_number }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $emp->name }}</span>
                            </td>

                            <!-- Department -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    {{ $emp->department->name ?? 'Belum Ditentukan' }}
                                </span>
                            </td>

                            <!-- Account Registration Status -->
                            <td class="py-3 px-4">
                                @if ($emp->registeredUser)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        {{ __('Akun Aktif') }} ({{ $emp->registeredUser->email }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        {{ __('Belum Registrasi') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400 text-[11px]">
                                {{ $emp->created_at ? $emp->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Actions (Edit & Delete) -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            wire:click="openEditModal({{ $emp->id }})"
                                            title="{{ __('Edit Karyawan') }}"
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
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto mb-4">{{ __('Gunakan tombol "Tambah Manual" atau "Import CSV" untuk mengisi Master Data HRD.') }}</p>
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" wire:click="openCreateModal" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs">{{ __('Tambah Manual') }}</button>
                                    <button type="button" wire:click="openCsvModal" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs">{{ __('Import CSV') }}</button>
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
    <!-- MODAL 1: TAMBAH / EDIT KARYAWAN MANUAL -->
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
                                {{ $editingEmployeeId ? __('Edit Data Karyawan') : __('Tambah Karyawan ke Master Data') }}
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                {{ __('Data ini menjadi acuan validasi NIK saat registrasi akun baru.') }}
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
                    <!-- KTP Number -->
                    <div>
                        <label for="form_ktp" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Nomor KTP (NIK 16 Digit)') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="form_ktp" 
                               wire:model="ktp_number" 
                               maxlength="16" 
                               placeholder="{{ __('Contoh: 3578012345670001') }}"
                               class="w-full text-xs font-mono px-3.5 py-2.5 rounded-lg border @error('ktp_number') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        @error('ktp_number') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="form_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Nama Lengkap Karyawan') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="form_name" 
                               wire:model="name" 
                               placeholder="{{ __('Contoh: Budi Santoso') }}"
                               class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('name') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        @error('name') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="form_dept" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('Departemen Penugasan') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="form_dept" 
                                wire:model="department_id"
                                class="w-full text-xs px-3.5 py-2.5 rounded-lg border @error('department_id') border-rose-400 bg-rose-50 dark:bg-rose-950/30 @else border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 @enderror focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition">
                            <option value="">{{ __('-- Pilih Departemen --') }}</option>
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
                            <span wire:loading.remove wire:target="saveEmployee">{{ $editingEmployeeId ? __('Simpan Perubahan') : __('Simpan Karyawan') }}</span>
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
                    {{ __('Konfirmasi Hapus Data Karyawan') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                    {{ __('Apakah Anda yakin ingin menghapus') }} <strong class="text-slate-800 dark:text-slate-200">{{ $deletingEmployeeName }}</strong> {{ __('dari Master Data HRD? Tindakan ini tidak dapat dibatalkan.') }}
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button type="button" 
                            wire:click="closeDeleteModal" 
                            class="w-1/2 py-2.5 px-3 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 transition cursor-pointer">
                        {{ __('Batal') }}
                    </button>

                    <button type="button" 
                            wire:click="deleteEmployee" 
                            class="w-1/2 py-2.5 px-3 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md transition cursor-pointer">
                        {{ __('Ya, Hapus') }}
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 3: BULK UPLOAD CSV MASTER DATA -->
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
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 w-full max-w-xl z-10 animate-fade-in text-slate-800 dark:text-slate-100 overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-800/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ __('Bulk Upload CSV Master Data Karyawan') }}
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                {{ __('Unggah berkas spreadsheet CSV untuk menyinkronkan data massal.') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            wire:click="closeCsvModal" 
                            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 text-xs">
                    
                    @if ($csvErrorMessage)
                        <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $csvErrorMessage }}</span>
                        </div>
                    @endif

                    @if ($csvSuccessMessage)
                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>{{ $csvSuccessMessage }}</span>
                        </div>
                    @endif

                    <!-- CSV Format Guide Box -->
                    <div class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-900/60 text-slate-700 dark:text-slate-300 space-y-2">
                        <div class="flex items-center gap-1.5 font-bold text-blue-900 dark:text-blue-300">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Ketentuan Format Kolom CSV (Headers):') }}</span>
                        </div>
                        <p class="text-[11px] leading-relaxed">
                            {{ __('Baris pertama wajib berisi header:') }} <code class="font-mono font-bold bg-white dark:bg-slate-800 px-1 py-0.5 rounded text-blue-700 dark:text-blue-300">ktp_number,name,department_id</code>
                        </p>
                        <div class="p-2 rounded bg-white dark:bg-slate-800 border border-blue-100 dark:border-blue-950 font-mono text-[10px] text-slate-600 dark:text-slate-400 overflow-x-auto">
ktp_number,name,department_id
3578015507940002,Siti Rahmawati,4
3578011203900001,Budi Pratama,1
3578012408880003,Agus Santoso,3
3578011805850004,Hendra Wijaya,5
                        </div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400">
                            <span class="font-bold">{{ __('ID Departemen:') }}</span>
                            @foreach ($departments as $d)
                                <span class="mr-2">{{ $d->id }} = {{ $d->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- File Upload Input Dropzone -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ __('Pilih File CSV (.csv)') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border-2 border-dashed border-gray-300 dark:border-slate-700 text-center">
                            <input type="file" 
                                   wire:model="csvFile" 
                                   accept=".csv,text/csv,text/plain"
                                   id="csv_file_input"
                                   class="text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                            <div wire:loading wire:target="csvFile" class="mt-2 text-[11px] text-blue-600 font-semibold flex items-center justify-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>{{ __('Mengunggah file sementara ke server...') }}</span>
                            </div>
                        </div>
                        @error('csvFile') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" 
                                wire:click="closeCsvModal" 
                                class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 transition cursor-pointer">
                            {{ __('Tutup') }}
                        </button>

                        <button type="button" 
                                wire:click="uploadCsv" 
                                wire:loading.attr="disabled"
                                class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                            <span wire:loading.remove wire:target="uploadCsv">{{ __('Proses Import CSV') }}</span>
                            <span wire:loading wire:target="uploadCsv" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>{{ __('Memproses...') }}</span>
                            </span>
                        </button>
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>
