<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-md p-4 sm:p-6 transition-colors duration-200"
     x-data="{
         activePoint: null,
         tooltipX: 0,
         tooltipY: 0,
         setTooltip(e, pt) {
             this.activePoint = pt;
             const rect = e.currentTarget.closest('svg').getBoundingClientRect();
             this.tooltipX = e.clientX - rect.left;
             this.tooltipY = e.clientY - rect.top;
         },
         clearTooltip() {
             this.activePoint = null;
         }
     }">

    <!-- Card Header: Title, Description, and Responsive Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </span>
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                    {{ __('Tren Permintaan Tiket Harian') }}
                </h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    {{ $days }} {{ __('Hari Terakhir') }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                {{ __('Pantau seberapa banyak request ticketing per harinya untuk mengukur beban penanganan, efektivitas respon, dan stabilitas operasional.') }}
            </p>
        </div>

        <!-- Filter Controls: Days Period, Department, Chart Type -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            
            <!-- Department Filter Dropdown -->
            <div class="relative">
                <select wire:model.live="departmentFilter" 
                    class="h-8 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-800/90 border border-gray-200 dark:border-slate-700 rounded-lg px-2.5 pr-7 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-2xs">
                    <option value="all">{{ __('Semua Departemen Tujuan') }}</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ __($dept->name) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Timeframe Selector Tabs (7, 14, 30 Hari) -->
            <div class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800/80 p-0.5 border border-gray-200 dark:border-slate-700/60 shadow-2xs">
                <button type="button" wire:click="setDays(7)" 
                    class="px-2.5 py-1 text-xs font-bold rounded-md transition cursor-pointer {{ $days === 7 ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    7H
                </button>
                <button type="button" wire:click="setDays(14)" 
                    class="px-2.5 py-1 text-xs font-bold rounded-md transition cursor-pointer {{ $days === 14 ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    14H
                </button>
                <button type="button" wire:click="setDays(30)" 
                    class="px-2.5 py-1 text-xs font-bold rounded-md transition cursor-pointer {{ $days === 30 ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    30H
                </button>
            </div>

            <!-- Chart Type Toggle (Line vs Bar) -->
            <div class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800/80 p-0.5 border border-gray-200 dark:border-slate-700/60 shadow-2xs">
                <button type="button" wire:click="setChartType('line')" 
                    class="p-1 rounded-md transition cursor-pointer {{ $chartType === 'line' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                    title="{{ __('Grafik Garis') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </button>
                <button type="button" wire:click="setChartType('bar')" 
                    class="p-1 rounded-md transition cursor-pointer {{ $chartType === 'bar' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                    title="{{ __('Grafik Batang') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Quick Metrics Cards (KPI Summary) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-4">
        <!-- 1. Total Permintaan -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-800/80">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">{{ __('Total Permintaan') }}</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $totalCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ __('tiket') }}</span>
            </div>
        </div>

        <!-- 2. Rata-rata per Hari -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-800/80">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">{{ __('Rata-rata / Hari') }}</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400">{{ $dailyAverage }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ __('tiket/hari') }}</span>
            </div>
        </div>

        <!-- 3. Hari Tersibuk -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-800/80">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">{{ __('Hari Puncak') }}</span>
            <div class="flex items-baseline gap-1.5 mt-1 truncate">
                <span class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400">{{ $peakCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    @if ($peakCount > 0)
                        ({{ $peakDate }})
                    @else
                        (-)
                    @endif
                </span>
            </div>
        </div>

        <!-- 4. Penyelesaian Tiket -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-gray-100 dark:border-slate-800/80">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">{{ __('Terselesaikan') }}</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $resolvedCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">({{ $resolutionRate }}%)</span>
            </div>
        </div>
    </div>

    <!-- MAIN CHART AREA (SVG Vector with Precision Coordinates) -->
    <div class="relative w-full overflow-hidden rounded-xl border border-gray-200/80 dark:border-slate-800/80 bg-slate-50/40 dark:bg-slate-950/40 p-2 sm:p-4">
        
        <!-- Empty Data Callout (Matches image 1 "No data recorded" when 0 tickets) -->
        @if ($totalCount === 0)
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] rounded-xl pointer-events-none">
                <div class="text-center p-4">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300 block">
                        {{ __('No data recorded') }}
                    </span>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                        {{ __('Belum ada permintaan tiket yang tercatat pada rentang periode ini.') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="relative w-full aspect-[16/7] min-h-[220px]">
            <svg viewBox="0 0 800 240" class="w-full h-full overflow-visible select-none" preserveAspectRatio="none">
                <defs>
                    <!-- Area Gradient Fill for Line Chart -->
                    <linearGradient id="ticketAreaGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.35" />
                        <stop offset="60%" stop-color="#3b82f6" stop-opacity="0.08" />
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
                    </linearGradient>

                    <!-- Bar Gradient Fill -->
                    <linearGradient id="ticketBarGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#3b82f6" />
                        <stop offset="100%" stop-color="#2563eb" />
                    </linearGradient>
                </defs>

                <!-- 1. Grid Lines and Y-Axis Scale Values (Matching Image 1: 0, 1, 2, 3, 4, 5...) -->
                @foreach ($gridLines as $grid)
                    <g class="transition-all">
                        <!-- Horizontal Grid Line -->
                        <line x1="{{ $chartLeft }}" y1="{{ $grid['y'] }}" x2="{{ $chartRight }}" y2="{{ $grid['y'] }}" 
                            class="stroke-gray-200 dark:stroke-slate-800 transition-colors" 
                            stroke-width="1" />
                        <!-- Y-Axis Label -->
                        <text x="{{ $chartLeft - 10 }}" y="{{ $grid['y'] + 4 }}" 
                            class="fill-slate-400 dark:fill-slate-500 text-[10px] font-mono font-medium" 
                            text-anchor="end">
                            {{ $grid['val'] }}
                        </text>
                    </g>
                @endforeach

                <!-- 2. Base X-Axis Axis Line -->
                <line x1="{{ $chartLeft }}" y1="{{ $chartBottom }}" x2="{{ $chartRight }}" y2="{{ $chartBottom }}" 
                    class="stroke-gray-300 dark:stroke-slate-700" 
                    stroke-width="1.5" />

                <!-- 3. RENDER DATA (LINE OR BAR) -->
                @if ($chartType === 'line')
                    <!-- Area Fill -->
                    @if (!empty($areaPath) && $totalCount > 0)
                        <path d="{{ $areaPath }}" fill="url(#ticketAreaGradient)" />
                    @endif

                    <!-- Line Stroke -->
                    @if (!empty($linePath))
                        <path d="{{ $linePath }}" 
                            fill="none" 
                            class="stroke-blue-600 dark:stroke-blue-400" 
                            stroke-width="2.5" 
                            stroke-linecap="round" 
                            stroke-linejoin="round" />
                    @endif

                    <!-- Data Point Circles with Interactive Hover Targets -->
                    @foreach ($dataPoints as $pt)
                        <g class="group cursor-pointer"
                           @mouseenter="setTooltip($event, {{ json_encode($pt) }})"
                           @mouseleave="clearTooltip()">
                            <!-- Visible Circle Marker -->
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="{{ $pt['count'] > 0 ? 4.5 : 3 }}" 
                                class="{{ $pt['count'] > 0 ? 'fill-blue-600 dark:fill-blue-400 stroke-white dark:stroke-slate-900 group-hover:scale-125 transition-transform' : 'fill-slate-300 dark:fill-slate-700 stroke-transparent' }}" 
                                stroke-width="2" />
                            
                            <!-- Invisible Large Target for Smooth Hovering -->
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="18" fill="transparent" />
                        </g>
                    @endforeach
                @else
                    <!-- Bar Chart Mode -->
                    @foreach ($dataPoints as $pt)
                        <g class="group cursor-pointer"
                           @mouseenter="setTooltip($event, {{ json_encode($pt) }})"
                           @mouseleave="clearTooltip()">
                            @if ($pt['barHeight'] > 0)
                                <rect x="{{ $pt['barX'] }}" y="{{ $pt['barY'] }}" width="{{ $pt['barWidth'] }}" height="{{ $pt['barHeight'] }}" 
                                    rx="3" 
                                    class="fill-blue-600 dark:fill-blue-500 hover:fill-blue-500 dark:hover:fill-blue-400 transition" />
                            @else
                                <!-- Subtle zero indicator -->
                                <rect x="{{ $pt['barX'] }}" y="{{ $chartBottom - 2 }}" width="{{ $pt['barWidth'] }}" height="2" 
                                    class="fill-gray-300 dark:fill-slate-800" />
                            @endif
                            <!-- Invisible Hover Area for entire vertical column -->
                            <rect x="{{ $pt['barX'] - 4 }}" y="{{ $chartBottom - 180 }}" width="{{ $pt['barWidth'] + 8 }}" height="180" fill="transparent" />
                        </g>
                    @endforeach
                @endif

                <!-- 4. X-Axis Date Labels -->
                @php
                    $stepInterval = $days === 30 ? 3 : ($days === 14 ? 1 : 1);
                @endphp
                @foreach ($dataPoints as $idx => $pt)
                    @if ($idx % $stepInterval === 0 || $idx === count($dataPoints) - 1)
                        <text x="{{ $pt['x'] }}" y="{{ $chartBottom + 18 }}" 
                            class="fill-slate-400 dark:fill-slate-500 text-[10px] font-medium" 
                            text-anchor="middle">
                            {{ $pt['label'] }}
                        </text>
                    @endif
                @endforeach
            </svg>
        </div>

        <!-- Floating Interactive Tooltip (Alpine.js) -->
        <div x-show="activePoint !== null" 
             x-cloak
             class="absolute z-30 pointer-events-none transform -translate-x-1/2 -translate-y-full mb-3"
             :style="`left: ${tooltipX}px; top: ${tooltipY}px;`"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-slate-900 text-white rounded-xl shadow-xl px-3 py-2 text-xs border border-slate-700 w-44 backdrop-blur-md">
                <div class="font-bold text-[11px] text-blue-300 border-b border-slate-800 pb-1 mb-1.5 flex items-center justify-between">
                    <span x-text="activePoint ? activePoint.fullDate : ''"></span>
                </div>
                <div class="flex items-center justify-between font-black text-sm text-white mb-1.5">
                    <span>{{ __('Total Tiket:') }}</span>
                    <span class="text-blue-400" x-text="activePoint ? activePoint.count + ' Tiket' : ''"></span>
                </div>
                <div class="space-y-1 text-[10px] text-slate-300 pt-1 border-t border-slate-800/80">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ __('Selesai') }}:</span>
                        <span class="font-bold text-emerald-400" x-text="activePoint ? activePoint.resolved : 0"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>{{ __('Diproses') }}:</span>
                        <span class="font-bold text-blue-400" x-text="activePoint ? activePoint.in_progress : 0"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>{{ __('Pending') }}:</span>
                        <span class="font-bold text-amber-400" x-text="activePoint ? activePoint.pending : 0"></span>
                    </div>
                </div>
            </div>
            <!-- Tooltip pointer arrow -->
            <div class="w-2.5 h-2.5 bg-slate-900 border-r border-b border-slate-700 transform rotate-45 mx-auto -mt-1.5"></div>
        </div>

    </div>

    <!-- Chart Footer Legend -->
    <div class="mt-3.5 pt-3 border-t border-gray-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-1 bg-blue-600 dark:bg-blue-400 rounded-full inline-block"></span>
                <span class="text-[11px]">{{ __('Volume Tiket Harian') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span class="text-[11px]">{{ __('Tiket Terselesaikan') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                <span class="text-[11px]">{{ __('Tiket Pending') }}</span>
            </div>
        </div>
        <div class="text-[11px] text-slate-400 dark:text-slate-500">
            {{ __('Diperbarui secara real-time setiap kali tiket dibuat atau diproses.') }}
        </div>
    </div>

</div>
