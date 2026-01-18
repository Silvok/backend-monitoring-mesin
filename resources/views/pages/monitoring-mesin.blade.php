<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script
            src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    @endpush
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">
                    Monitoring & Analisis Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div
                    class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Terhubung</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Clean Industry-Standard Filter Card with Green Accents -->
            <div style="border-radius: 24px !important;"
                class="bg-white border border-gray-100 shadow-sm p-8 mb-8 uppercase tracking-tight relative overflow-hidden">
                <!-- Top Accent Bar -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-emerald-500/80"></div>

                <!-- Header "Filters" -->
                <div class="flex items-center space-x-2.5 mb-8 border-b border-gray-50 pb-4">
                    <div style="border-radius: 10px !important;" class="p-1.5 bg-emerald-50">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Filters</h3>
                </div>

                <!-- Three Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Column 1: Machine -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                            <span class="text-sm font-bold tracking-wide">Machine</span>
                        </div>
                        <div class="relative group">
                            <select id="filter-machine" onchange="applyFilter()" style="border-radius: 12px !important;"
                                class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                                <option value="">All Systems</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Column 2: Analysis Axis -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-bold tracking-wide">Analysis Axis</span>
                        </div>
                        <div class="relative group">
                            <select id="filter-axis" onchange="applyFilter()" style="border-radius: 12px !important;"
                                class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                                <option value="x">X-Axis Horizontal</option>
                                <option value="y">Y-Axis Vertical</option>
                                <option value="z">Z-Axis Longitudinal</option>
                                <option value="resultant" selected>Total RMS</option>
                            </select>
                        </div>
                    </div>

                    <!-- Column 3: Time Range -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-bold tracking-wide">Time Period</span>
                        </div>
                        <div class="relative group">
                            <select id="filter-time-range" onchange="applyFilter()"
                                style="border-radius: 12px !important;"
                                class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                                <option value="realtime">Live Monitoring</option>
                                <option value="1h">Last 1 Hour</option>
                                <option value="24h">Last 24 Hours</option>
                                <option value="7d">Last 7 Days</option>
                                <option value="all" selected>Full History</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matched Reference Module Switcher -->
            <div class="flex justify-center mb-10">
                <div class="bg-[#F1F5F9]/80 p-1 rounded-full flex items-center shadow-inner border border-gray-100">
                    <!-- Button: Grafik -->
                    <button onclick="switchModule('grafik')" id="btn-modul-grafik"
                        class="flex items-center space-x-2 px-8 py-2.5 rounded-full font-medium text-sm transition-all duration-300 bg-white shadow-sm text-emerald-600 group">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span>Grafik</span>
                    </button>

                    <!-- Button: Analisis -->
                    <button onclick="switchModule('analisis')" id="btn-modul-analisis"
                        class="flex items-center space-x-2 px-8 py-2.5 rounded-full font-medium text-sm transition-all duration-300 text-slate-500 hover:text-emerald-500 group">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Analisis</span>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div id="module-container" class="space-y-6">
                <!-- VIEW: GRAFIK -->
                <div id="section-grafik" class="space-y-6 animate-fade-in">
                    <!-- Modul Grafik: Time Domain -->
                    <div class="grid grid-cols-1 gap-6">
                        <div style="border-radius: 16px !important;"
                            class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col h-[520px]">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Time Domain</h3>
                                    <p class="text-[12px] text-gray-500 font-medium">Visualisasi amplitudo getaran (RMS)
                                        dan suhu terhadap waktu</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <!-- Reset Zoom Button -->
                                    <button onclick="resetZoom()"
                                        class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all"
                                        title="Reset Zoom">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    <!-- Dataset Toggles -->
                                    <div class="flex bg-gray-100 p-1 rounded-lg">
                                        <button onclick="toggleDataset(0)" id="btn-rms"
                                            class="px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-emerald-600 transition-all">GETARAN</button>
                                        <button onclick="toggleDataset(1)" id="btn-temp"
                                            class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-red-500 transition-all">SUHU</button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-grow relative min-h-0">
                                <canvas id="timeDomainChart"></canvas>
                                <!-- Loading / Empty State Placeholder -->
                                <div id="chartPlaceholder"
                                    class="absolute inset-0 flex items-center justify-center bg-gray-50/50 rounded-xl border border-dashed border-gray-200 z-10 transition-opacity">
                                    <div class="text-center">
                                        <div class="p-4 bg-white rounded-full shadow-sm inline-block mb-3">
                                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-600">Pilih mesin untuk menampilkan grafik
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-1">Gunakan wheel mouse untuk zoom, drag
                                            untuk
                                            pan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Frequency Domain Analysis Module (FFT) -->
                    <div style="border-radius: 16px !important;"
                        class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col h-[520px]">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Frequency Domain
                                    (FFT)
                                </h3>
                                <p class="text-[12px] text-gray-500 font-medium">Visualisasi spektrum frekuensi hasil
                                    Fast
                                    Fourier Transform</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <!-- Frequency Info Badge -->
                                <div id="fft-result-info"
                                    class="hidden flex items-center space-x-3 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                                    <span
                                        class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Puncak:</span>
                                    <span class="text-xs font-black text-blue-700" id="dominant-freq">0</span>
                                    <span class="text-[10px] font-bold text-blue-600">Hz</span>
                                </div>

                                <!-- Reset Zoom Button -->
                                <button onclick="resetZoom()"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                    title="Reset Zoom">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex-grow relative min-h-0">
                            <canvas id="fftChart"></canvas>
                            <!-- FFT Loading / Empty State -->
                            <div id="fftChartPlaceholder"
                                class="absolute inset-0 flex items-center justify-center bg-gray-50/50 rounded-xl border border-dashed border-gray-200 z-10 transition-opacity">
                                <div class="text-center">
                                    <div class="p-4 bg-white rounded-full shadow-sm inline-block mb-3">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600">Menunggu data FFT dari mesin...</p>
                                    <p class="text-gray-400 mt-1 uppercase tracking-widest" style="font-size: 9px;">
                                        Spectrum
                                        akan diupdate otomatis</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer: Frequency Bands -->
                        <div class="mt-4 flex flex-wrap items-center gap-4 pt-3 border-t border-gray-50">
                            <div class="flex items-center space-x-1.5">
                                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                <span class="font-black text-gray-400 uppercase tracking-tight"
                                    style="font-size: 9px;">Low
                                    (0-100Hz)</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="font-black text-gray-400 uppercase tracking-tight"
                                    style="font-size: 9px;">Mid
                                    (100-500Hz)</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                                <span class="font-black text-gray-400 uppercase tracking-tight"
                                    style="font-size: 9px;">High
                                    (500Hz+)</span>
                            </div>
                            <div class="flex-grow"></div>
                            <p class="text-gray-400/70 italic" style="font-size: 9px;">Dianalisis dari batch sensor
                                terbaru
                            </p>
                        </div>
                    </div>
                </div>

                <div id="section-analisis" class="space-y-6 hidden animate-fade-in">
                    <!-- ============================================== -->
                    <!-- CARD: ANALISIS TREN - EARLY WARNING DETECTION -->
                    <!-- ============================================== -->
                    <div id="early-warning-card" style="border-radius: 16px !important;"
                        class="bg-white border border-gray-100 shadow-sm overflow-hidden transition-all duration-500">
                        <!-- Header dengan gradient -->
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-blue-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-emerald-100 rounded-lg">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Analisis Tren RMS</h3>
                                        <p class="text-sm text-gray-500">Early Warning Detection System</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">24 Jam Terakhir</span>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Alert Box - Trend Warning -->
                            <div id="trend-alert-box" class="mb-6 p-4 rounded-xl border-2 bg-blue-50 border-blue-200 flex items-start gap-4 transition-all duration-500">
                                <div class="flex-shrink-0">
                                    <svg id="trend-alert-icon" class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 id="trend-alert-title" class="font-bold text-blue-800 text-lg mb-1">
                                        ℹ️ Pilih mesin untuk melihat analisis tren
                                    </h4>
                                    <p id="trend-alert-desc" class="text-sm text-blue-700 opacity-80">
                                        <strong>INFO:</strong> Silakan pilih mesin dari filter untuk memulai analisis.
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <div id="trend-change-percent" class="text-3xl font-black text-blue-800">--%</div>
                                    <div class="text-xs text-blue-600 opacity-60">Perubahan RMS</div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <!-- Current RMS -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <span class="text-xs font-medium text-gray-500 uppercase">RMS Rata-rata</span>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">
                                        <span id="trend-current-avg">0.0000</span> <span class="text-sm font-normal text-gray-500">mm/s</span>
                                    </div>
                                </div>

                                <!-- Max RMS -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <span class="text-xs font-medium text-gray-500 uppercase">RMS Maksimum</span>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">
                                        <span id="trend-current-max">0.0000</span> <span class="text-sm font-normal text-gray-500">mm/s</span>
                                    </div>
                                </div>

                                <!-- Min RMS -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                        <span class="text-xs font-medium text-gray-500 uppercase">RMS Minimum</span>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">
                                        <span id="trend-current-min">0.0000</span> <span class="text-sm font-normal text-gray-500">mm/s</span>
                                    </div>
                                </div>

                                <!-- Trend Direction -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                        <span class="text-xs font-medium text-gray-500 uppercase">Arah Tren</span>
                                    </div>
                                    <div id="trend-direction-box" class="text-lg font-bold flex items-center gap-2 text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                                        </svg>
                                        <span id="trend-direction-text">Menunggu</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Rekomendasi -->
                            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <div>
                                        <h5 class="font-semibold text-blue-800 mb-1">Rekomendasi Tindakan</h5>
                                        <p id="trend-recommendation" class="text-sm text-blue-700">Pilih mesin untuk melihat rekomendasi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NEW: Modul Analisis RMS (Decision Layer) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Status Kondisi Card -->
                        <div id="status-card" style="border-radius: 16px !important;"
                            class="bg-white border border-gray-100 shadow-sm p-6 flex flex-col items-center justify-center text-center relative overflow-hidden min-h-[220px] transition-all duration-500">
                            <!-- Background Accent -->
                            <div id="status-bg" class="absolute inset-0 opacity-0 transition-opacity duration-500">
                            </div>

                            <div class="relative z-10 w-full flex flex-col items-center">
                                <h3 id="status-label"
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Status
                                    Kondisi</h3>

                                <div id="status-indicator"
                                    class="w-16 h-16 rounded-full border-4 border-gray-100 flex items-center justify-center mb-4 transition-all duration-500 relative shadow-sm bg-gray-50/50">
                                    <svg id="status-icon" class="w-8 h-8 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <h2 id="status-text"
                                    class="text-2xl font-black text-gray-400 uppercase tracking-tight mb-3">MENUNGGU
                                    DATA</h2>

                                <div id="interpretation-box"
                                    class="px-3 py-2.5 bg-gray-50 rounded-xl border border-gray-100 w-full transition-colors duration-500">
                                    <p id="status-interpretation"
                                        class="text-[11px] text-gray-500 font-bold leading-tight">
                                        Silakan pilih mesin untuk memulai analisis
                                    </p>
                                </div>

                                <div class="mt-4 flex items-center space-x-1.5 opacity-50">
                                    <div id="iso-dot" class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                    <span
                                        class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">ISO
                                        10816-3 Thresholds</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current RMS Metric Card -->
                        <div style="border-radius: 16px !important;"
                            class="bg-white border border-gray-100 shadow-sm p-6 group hover:border-emerald-200 hover:shadow-md transition-all flex items-center space-x-6">
                            <div
                                class="flex-shrink-0 p-3 bg-emerald-50 rounded-2xl group-hover:bg-emerald-500 transition-colors duration-300">
                                <svg class="w-6 h-6 text-emerald-500 group-hover:text-white" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 truncate">
                                    RMS Saat Ini</p>
                                <div class="flex flex-wrap items-baseline gap-x-1">
                                    <span id="analysis-rms-current"
                                        class="text-2xl font-black text-gray-900 tracking-tight leading-none">0.000</span>
                                    <span class="text-[9px] font-bold text-gray-400">mm/s</span>
                                </div>
                            </div>
                        </div>

                        <!-- Average RMS Metric Card -->
                        <div style="border-radius: 16px !important;"
                            class="bg-white border border-gray-100 shadow-sm p-6 group hover:border-blue-200 hover:shadow-md transition-all flex items-center space-x-6">
                            <div
                                class="flex-shrink-0 p-3 bg-blue-50 rounded-2xl group-hover:bg-blue-500 transition-colors duration-300">
                                <svg class="w-6 h-6 text-blue-500 group-hover:text-white" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 truncate">
                                    Rata-rata RMS</p>
                                <div class="flex flex-wrap items-baseline gap-x-1">
                                    <span id="analysis-rms-avg"
                                        class="text-2xl font-black text-gray-900 tracking-tight leading-none">0.000</span>
                                    <span class="text-[9px] font-bold text-gray-400">mm/s</span>
                                </div>
                            </div>
                        </div>

                        <!-- Max RMS Metric Card -->
                        <div style="border-radius: 16px !important;"
                            class="bg-white border border-gray-100 shadow-sm p-6 group hover:border-red-200 hover:shadow-md transition-all flex items-center space-x-6">
                            <div
                                class="flex-shrink-0 p-3 bg-red-50 rounded-2xl group-hover:bg-red-500 transition-colors duration-300">
                                <svg class="w-6 h-6 text-red-500 group-hover:text-white" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 truncate">
                                    RMS Maksimum</p>
                                <div class="flex flex-wrap items-baseline gap-x-1">
                                    <span id="analysis-rms-max"
                                        class="text-2xl font-black text-gray-900 tracking-tight leading-none">0.000</span>
                                    <span class="text-[9px] font-bold text-gray-400">mm/s</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NEW: Modul Diagnosa FFT (Fault Identification) -->
                    <div id="fft-diagnostic-card" style="border-radius: 16px !important;"
                        class="bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden transition-all duration-500">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <!-- Left Side: Basic Info -->
                            <div class="flex-1 space-y-6">
                                <div>
                                    <h3 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-4">
                                        Analisis Kerahasiaan FFT (Diagnosa)</h3>
                                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Kerusakan apa yang berpotensi terjadi?</h2>
                                    <p class="text-xs text-gray-500 mt-1 italic">Analisis otomatis berdasarkan pola spektrum frekuensi</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Frekuensi Dominan</p>
                                        <div class="flex items-baseline space-x-1">
                                            <span id="diagnostic-freq" class="text-xl font-black text-gray-900">0</span>
                                            <span class="text-[10px] font-bold text-gray-400">Hz</span>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Amplitudo Tertinggi</p>
                                        <div class="flex items-baseline space-x-1">
                                            <span id="diagnostic-amp" class="text-xl font-black text-gray-900">0.00</span>
                                            <span class="text-[10px] font-bold text-gray-400">mm/s</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Middle Section: Fault Indicators -->
                            <div class="flex-1 bg-gray-50/50 rounded-2xl p-5 border border-gray-100/50">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Indikasi Kerusakan</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div id="indicator-unbalance" class="flex items-center space-x-2 grayscale opacity-40 transition-all duration-500">
                                        <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                        <span class="text-xs font-bold text-gray-700">Unbalance</span>
                                    </div>
                                    <div id="indicator-misalignment" class="flex items-center space-x-2 grayscale opacity-40 transition-all duration-500">
                                        <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                                        <span class="text-xs font-bold text-gray-700">Misalignment</span>
                                    </div>
                                    <div id="indicator-bearing" class="flex items-center space-x-2 grayscale opacity-40 transition-all duration-500">
                                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                        <span class="text-xs font-bold text-gray-700">Bearing Defect</span>
                                    </div>
                                    <div id="indicator-looseness" class="flex items-center space-x-2 grayscale opacity-40 transition-all duration-500">
                                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                                        <span class="text-xs font-bold text-gray-700">Looseness</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side: Final Verdict -->
                            <div class="flex-1 flex flex-col items-center justify-center text-center p-6 border-l md:border-dashed border-gray-200">
                                <div class="p-3 bg-emerald-50 rounded-2xl mb-3">
                                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-1.03 0-1.9-.4-2.593-.895l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <p id="diagnostic-verdict" class="text-sm font-bold text-emerald-700 leading-tight">
                                    "Kondisi mesin terpantau optimal tanpa indikasi kerusakan frekuensi yang signifikan."
                                </p>
                            </div>
                        </div>

                        <!-- Subtle background glow -->
                        <div id="diagnostic-glow" class="absolute -right-20 -bottom-20 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-50 transition-colors duration-500"></div>
                    </div>

                    <!-- Long-term Trend Analysis Module (OPSIONAL tapi KUAT) -->
                    <div style="border-radius: 16px !important;"
                        class="bg-white shadow-sm border border-gray-100 p-8 flex flex-col h-[520px]">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Tren Kondisi
                                    (Riwayat)
                                </h3>
                                <p class="text-[12px] text-gray-500 font-medium">Monitoring tren degradasi mesin melalui
                                    Moving
                                    Average RMS harian</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <!-- Trend View Toggles -->
                                <div class="flex bg-gray-50 p-1 rounded-2xl">
                                    <button onclick="setTrendPeriod('daily')" id="btn-trend-daily"
                                        class="px-4 py-1.5 text-xs font-bold rounded-xl bg-white shadow-sm text-emerald-600 transition-all uppercase tracking-widest">Harian</button>
                                    <button onclick="setTrendPeriod('weekly')" id="btn-trend-weekly"
                                        class="px-4 py-1.5 text-xs font-bold rounded-xl text-gray-400 hover:text-emerald-500 transition-all uppercase tracking-widest">Mingguan</button>
                                </div>

                                <!-- Reset Zoom Button -->
                                <button onclick="trendChart.resetZoom()"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                    title="Reset Zoom">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex-grow relative min-h-0">
                            <canvas id="trendChart"></canvas>
                            <!-- Trend Loading / Empty State -->
                            <div id="trendChartPlaceholder"
                                class="absolute inset-0 flex items-center justify-center bg-gray-50/50 rounded-3xl border border-dashed border-gray-200 z-10 transition-opacity">
                                <div class="text-center">
                                    <div class="p-4 bg-white rounded-full shadow-sm inline-block mb-3">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600">Pilih mesin untuk memuat tren riwayat</p>
                                    <p class="text-gray-400 mt-1 uppercase tracking-widest" style="font-size: 9px;">Data
                                        tren
                                        dihitung otomatis dari riwayat database</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer: Trend Indicators -->
                        <div class="mt-6 flex flex-wrap items-center gap-6 pt-4 border-t border-gray-50">
                            <div class="flex items-center space-x-1.5">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="font-black text-gray-400 uppercase tracking-tight"
                                    style="font-size: 9px;">Avg
                                    RMS</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.4)]">
                                </div>
                                <span class="font-black text-gray-400 uppercase tracking-tight"
                                    style="font-size: 9px;">SMA
                                    7-Day</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div class="w-2 h-2 rounded-lg bg-orange-200 border border-orange-400"></div>
                                <span class="font-black text-gray-400 uppercase tracking-tight"
                                    style="font-size: 9px;">Max
                                    Peak
                                    Area</span>
                            </div>
                            <div class="flex-grow"></div>
                            <p class="text-gray-400/70 italic" style="font-size: 9px;">Sangat berguna untuk bukti visual
                                pemeliharaan predictif (PdM)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                let timeChart, fftChart, trendChart;
                let currentTrendPeriod = 'daily';

                function switchModule(type) {
                    const sectionGrafik = document.getElementById('section-grafik');
                    const sectionAnalisis = document.getElementById('section-analisis');
                    const btnGrafik = document.getElementById('btn-modul-grafik');
                    const btnAnalisis = document.getElementById('btn-modul-analisis');

                    const activeClass = "flex items-center space-x-2 px-8 py-2.5 rounded-full font-medium text-sm transition-all duration-300 bg-white shadow-sm text-emerald-600 group";
                    const inactiveClass = "flex items-center space-x-2 px-8 py-2.5 rounded-full font-medium text-sm transition-all duration-300 text-slate-500 hover:text-emerald-500 group";

                    if (type === 'grafik') {
                        sectionGrafik.classList.remove('hidden');
                        sectionAnalisis.classList.add('hidden');
                        btnGrafik.className = activeClass;
                        btnAnalisis.className = inactiveClass;
                        btnGrafik.querySelector('svg').className = "w-5 h-5 text-emerald-500";
                        btnAnalisis.querySelector('svg').className = "w-5 h-5 text-slate-400 group-hover:text-emerald-400";
                        if (timeChart) timeChart.update();
                        if (fftChart) fftChart.update();
                    } else {
                        sectionGrafik.classList.add('hidden');
                        sectionAnalisis.classList.remove('hidden');
                        btnAnalisis.className = activeClass;
                        btnGrafik.className = inactiveClass;
                        btnAnalisis.querySelector('svg').className = "w-5 h-5 text-emerald-500";
                        btnGrafik.querySelector('svg').className = "w-5 h-5 text-slate-400 group-hover:text-emerald-400";
                        if (trendChart) trendChart.update();
                    }
                }

                function initCharts() {
                    try {
                        console.log("Initializing charts...");
                        initTimeChart();
                        initFFTChart();
                        initTrendChart();
                        console.log("Charts initialized successfully.");
                    } catch (e) {
                        console.error("Error during chart initialization:", e);
                    }
                }

                function initTimeChart() {
                    const ctx = document.getElementById('timeDomainChart').getContext('2d');
                    timeChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            datasets: [
                                {
                                    label: 'Vibration RMS (mm/s)',
                                    data: [],
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 2.5,
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 4,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Temperature (°C)',
                                    data: [],
                                    borderColor: '#ef4444',
                                    backgroundColor: 'transparent',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    tension: 0.3,
                                    pointRadius: 0,
                                    pointHoverRadius: 4,
                                    hidden: true,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    titleColor: '#1f2937',
                                    bodyColor: '#4b5563',
                                    borderColor: '#e5e7eb',
                                    borderWidth: 1,
                                    padding: 12,
                                    bodyFont: { size: 12, weight: '500' },
                                    titleFont: { size: 13, weight: '700' },
                                    callbacks: {
                                        title: function (context) {
                                            const date = new Date(context[0].parsed.x);
                                            return date.toLocaleString('id-ID', {
                                                day: '2-digit', month: '2-digit', year: 'numeric',
                                                hour: '2-digit', minute: '2-digit', second: '2-digit'
                                            });
                                        },
                                        label: function (context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.parsed.y !== null) label += context.parsed.y.toFixed(3);
                                            return label;
                                        }
                                    }
                                },
                                zoom: {
                                    pan: {
                                        enabled: true,
                                        mode: 'x',
                                    },
                                    zoom: {
                                        wheel: { enabled: true },
                                        pinch: { enabled: true },
                                        mode: 'x',
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'minute',
                                        displayFormats: {
                                            minute: 'HH:mm',
                                            hour: 'HH:mm',
                                            day: 'dd/MM'
                                        }
                                    },
                                    grid: { display: false },
                                    ticks: {
                                        maxTicksLimit: 10,
                                        font: { size: 10, weight: '500' },
                                        color: '#9ca3af'
                                    }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: { display: true, text: 'RMS (mm/s)', font: { weight: 'bold', size: 11 } },
                                    beginAtZero: true,
                                    ticks: { font: { size: 10 } }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: { display: true, text: 'Temp (°C)', font: { weight: 'bold', size: 11 } },
                                    grid: { drawOnChartArea: false },
                                    beginAtZero: false,
                                    ticks: { font: { size: 10 } }
                                }
                            }
                        }
                    });
                }

                function initFFTChart() {
                    const ctx = document.getElementById('fftChart').getContext('2d');
                    fftChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Amplitude',
                                data: [],
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                tension: 0.1,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#2563eb'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        title: (items) => `Frekuensi: ${items[0].label} Hz`,
                                        label: (item) => `Amplitudo: ${item.parsed.y.toFixed(4)}`
                                    }
                                },
                                zoom: {
                                    pan: { enabled: true, mode: 'x' },
                                    zoom: {
                                        wheel: { enabled: true },
                                        pinch: { enabled: true },
                                        mode: 'x',
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: { display: true, text: 'Frequency (Hz)', font: { weight: 'bold', size: 10 } },
                                    ticks: { font: { size: 9 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Amplitude', font: { weight: 'bold', size: 10 } },
                                    ticks: { font: { size: 9 } }
                                }
                            }
                        },
                        plugins: [{
                            id: 'bandBackgrounds',
                            beforeDraw: (chart) => {
                                if (!chart.chartArea || !chart.scales.x || !chart.scales.y) return;
                                const { ctx, chartArea: { top, bottom, left, right }, scales: { x, y } } = chart;
                                function drawBand(startHz, endHz, color) {
                                    const startX = x.getPixelForValue(startHz);
                                    const endX = x.getPixelForValue(endHz);
                                    if (startX >= left && startX <= right) {
                                        ctx.fillStyle = color;
                                        ctx.fillRect(startX, top, Math.min(endX, right) - startX, bottom - top);
                                    }
                                }
                                ctx.save();
                                drawBand(0, 100, 'rgba(96, 165, 250, 0.03)');   // Low
                                drawBand(100, 500, 'rgba(52, 211, 153, 0.03)'); // Mid
                                drawBand(500, 2000, 'rgba(251, 146, 60, 0.03)'); // High
                                ctx.restore();
                            }
                        }]
                    });
                }

                function initTrendChart() {
                    const ctx = document.getElementById('trendChart').getContext('2d');
                    trendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [
                                {
                                    label: 'SMA 7-Period',
                                    data: [],
                                    borderColor: '#10b981',
                                    backgroundColor: 'transparent',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    pointRadius: 0,
                                    order: 1
                                },
                                {
                                    label: 'Avg RMS',
                                    data: [],
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                    pointBackgroundColor: '#3b82f6',
                                    order: 2
                                },
                                {
                                    label: 'Max Peak Area',
                                    data: [],
                                    borderColor: 'rgba(249, 115, 22, 0.3)',
                                    backgroundColor: 'rgba(249, 115, 22, 0.05)',
                                    borderWidth: 1,
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 0,
                                    order: 3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    padding: 10,
                                    callbacks: {
                                        label: function (context) {
                                            return `${context.dataset.label}: ${context.parsed.y.toFixed(4)} mm/s`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 9 }, color: '#9ca3af' }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'RMS (mm/s)', font: { size: 10, weight: 'bold' } },
                                    ticks: { font: { size: 9 } }
                                }
                            }
                        }
                    });
                }

                async function setTrendPeriod(period) {
                    currentTrendPeriod = period;

                    // Update UI Toggles
                    const btnDaily = document.getElementById('btn-trend-daily');
                    const btnWeekly = document.getElementById('btn-trend-weekly');

                    if (period === 'daily') {
                        btnDaily.className = "px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-emerald-600 transition-all uppercase tracking-widest";
                        btnWeekly.className = "px-4 py-1.5 text-xs font-bold rounded-md text-gray-400 hover:text-emerald-500 transition-all uppercase tracking-widest";
                    } else {
                        btnWeekly.className = "px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-emerald-600 transition-all uppercase tracking-widest";
                        btnDaily.className = "px-4 py-1.5 text-xs font-bold rounded-md text-gray-400 hover:text-emerald-500 transition-all uppercase tracking-widest";
                    }

                    await fetchTrendData();
                }

                async function fetchTrendData() {
                    const machineId = document.getElementById('filter-machine').value;
                    if (!machineId) return;

                    console.log(`Fetching trend data for period: ${currentTrendPeriod}`);

                    try {
                        const response = await fetch(`/api/monitoring/trend?machine_id=${machineId}&period=${currentTrendPeriod}`);

                        if (!response.ok) {
                            console.error(`Trend Server error: ${response.status}`);
                            return;
                        }

                        const data = await response.json();
                        console.log("Trend Data:", data);

                        if (data.status === 'success') {
                            const trendPlaceholder = document.getElementById('trendChartPlaceholder');

                            if (data.trend && data.trend.length > 0) {
                                if (trendPlaceholder) trendPlaceholder.classList.add('hidden');

                                if (trendChart) {
                                    const labels = data.trend.map(d => d.label);
                                    const avgValues = data.trend.map(d => d.avg_rms);
                                    const maxValues = data.trend.map(d => d.max_rms);

                                    // Calculate simple moving average (SMA) 7-period
                                    const smaValues = avgValues.map((val, idx, arr) => {
                                        if (idx < 6) return null;
                                        const slice = arr.slice(idx - 6, idx + 1);
                                        return slice.reduce((a, b) => a + b, 0) / 7;
                                    });

                                    trendChart.data.labels = labels;
                                    trendChart.data.datasets[0].data = smaValues;
                                    trendChart.data.datasets[1].data = avgValues;
                                    trendChart.data.datasets[2].data = maxValues;

                                    trendChart.update();
                                }
                            } else {
                                if (trendPlaceholder) {
                                    trendPlaceholder.classList.remove('hidden');
                                    const msg = trendPlaceholder.querySelector('p.text-sm');
                                    if (msg) msg.textContent = "Belum ada riwayat data untuk mesin ini";
                                }
                                if (trendChart) {
                                    trendChart.data.labels = [];
                                    trendChart.data.datasets.forEach(ds => ds.data = []);
                                    trendChart.update();
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching trend data:', error);
                    }
                }

                function toggleDataset(index) {
                    const isVisible = timeChart.setDatasetVisibility(index, !timeChart.isDatasetVisible(index));
                    timeChart.update();

                    const btnRms = document.getElementById('btn-rms');
                    const btnTemp = document.getElementById('btn-temp');

                    if (index === 0) { // RMS
                        btnRms.className = timeChart.isDatasetVisible(0)
                            ? "px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-emerald-600 transition-all border border-emerald-50"
                            : "px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-emerald-500 transition-all";
                    } else { // Temp
                        btnTemp.className = timeChart.isDatasetVisible(1)
                            ? "px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-red-600 transition-all border border-red-50"
                            : "px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-red-500 transition-all";
                    }
                }

                function resetZoom() {
                    if (timeChart) timeChart.resetZoom();
                    if (fftChart) fftChart.resetZoom();
                }

                async function applyFilter() {
                    const machineId = document.getElementById('filter-machine').value;
                    const range = document.getElementById('filter-time-range').value;
                    const axis = document.getElementById('filter-axis').value;

                    if (!machineId) return;

                    console.log("Applying filter...");

                    // Show placeholders
                    const timePlaceholder = document.getElementById('chartPlaceholder');
                    const fftPlaceholder = document.getElementById('fftChartPlaceholder');
                    const trendPlaceholder = document.getElementById('trendChartPlaceholder');

                    if (timePlaceholder) timePlaceholder.classList.remove('hidden');
                    if (fftPlaceholder) fftPlaceholder.classList.remove('hidden');
                    if (trendPlaceholder) trendPlaceholder.classList.remove('hidden');
                    document.getElementById('fft-result-info').classList.add('hidden');

                    try {
                        const response = await fetch(`/api/monitoring/data?machine_id=${machineId}&range=${range}&axis=${axis}`);

                        if (!response.ok) {
                            console.error(`Server error: ${response.status}`);
                            return;
                        }

                        const data = await response.json();
                        console.log("Monitoring Data:", data);

                        if (data.status === 'success') {
                            // Update Time Domain
                            if (timePlaceholder) timePlaceholder.classList.add('hidden');

                            if (timeChart && data.time_domain) {
                                timeChart.data.datasets[0].data = data.time_domain.vibration;
                                timeChart.data.datasets[1].data = data.time_domain.temperature;

                                if (range === 'realtime') {
                                    timeChart.options.scales.x.time.unit = 'second';
                                } else if (range === '1h') {
                                    timeChart.options.scales.x.time.unit = 'minute';
                                } else if (range === '7d' || range === 'all') {
                                    timeChart.options.scales.x.time.unit = 'day';
                                }
                                timeChart.update();
                            }

                            // Update Frequency Domain (FFT)
                            if (data.frequency_domain) {
                                if (fftPlaceholder) fftPlaceholder.classList.add('hidden');
                                document.getElementById('fft-result-info').classList.remove('hidden');
                                document.getElementById('dominant-freq').textContent = data.frequency_domain.dominant_freq_hz.toFixed(2);

                                if (fftChart) {
                                    fftChart.data.labels = data.frequency_domain.frequencies;
                                    fftChart.data.datasets[0].data = data.frequency_domain.amplitudes;

                                    const domFreq = data.frequency_domain.dominant_freq_hz;
                                    fftChart.data.datasets[0].pointRadius = data.frequency_domain.frequencies.map(f =>
                                        Math.abs(f - domFreq) < 1 ? 5 : 0
                                    );
                                    fftChart.update();
                                }

                                // NEW: Update Analysis Diagnostic
                                updateFFTDiagnostic(data.frequency_domain.frequencies.map((f, i) => ({
                                    x: f,
                                    y: data.frequency_domain.amplitudes[i]
                                })));
                            }

                            // Update Analysis Module (Decision Layer)
                            updateAnalysisModule(data.time_domain.vibration);

                            // Load Trend Data
                            fetchTrendData();

                            // Load Trend Analysis (Early Warning)
                            fetchTrendAnalysis();
                        }
                    } catch (error) {
                        console.error('Error fetching data:', error);
                    }
                }

                // =====================
                // TREND ANALYSIS - Early Warning Detection
                // =====================
                async function fetchTrendAnalysis() {
                    const machineId = document.getElementById('filter-machine').value;
                    if (!machineId) return;

                    console.log("Fetching trend analysis for early warning...");

                    try {
                        const response = await fetch(`/api/monitoring/trend-analysis?machine_id=${machineId}`);

                        if (!response.ok) {
                            console.error(`Trend Analysis Server error: ${response.status}`);
                            return;
                        }

                        const data = await response.json();
                        console.log("Trend Analysis Data:", data);

                        if (data.status === 'success' && data.trend_analysis) {
                            updateEarlyWarningUI(data.trend_analysis);
                        }
                    } catch (error) {
                        console.error('Error fetching trend analysis:', error);
                    }
                }

                function updateEarlyWarningUI(analysis) {
                    const alertBox = document.getElementById('trend-alert-box');
                    const alertIcon = document.getElementById('trend-alert-icon');
                    const alertTitle = document.getElementById('trend-alert-title');
                    const alertDesc = document.getElementById('trend-alert-desc');
                    const changePercent = document.getElementById('trend-change-percent');
                    const currentAvg = document.getElementById('trend-current-avg');
                    const currentMax = document.getElementById('trend-current-max');
                    const currentMin = document.getElementById('trend-current-min');
                    const directionBox = document.getElementById('trend-direction-box');
                    const directionText = document.getElementById('trend-direction-text');
                    const recommendation = document.getElementById('trend-recommendation');

                    // Update stats
                    currentAvg.textContent = analysis.current_avg.toFixed(4);
                    currentMax.textContent = analysis.current_max.toFixed(4);
                    currentMin.textContent = analysis.current_min.toFixed(4);
                    recommendation.textContent = analysis.recommendation;

                    // Update change percent
                    const percent = analysis.change_percent;
                    changePercent.textContent = (percent > 0 ? '+' : '') + percent + '%';

                    // Update direction
                    let directionIcon = '';
                    let directionLabel = '';
                    let directionColor = 'text-gray-600';

                    if (analysis.trend_direction === 'increasing') {
                        directionIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>';
                        directionLabel = 'Naik';
                        directionColor = 'text-red-600';
                    } else if (analysis.trend_direction === 'decreasing') {
                        directionIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>';
                        directionLabel = 'Turun';
                        directionColor = 'text-emerald-600';
                    } else {
                        directionIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path></svg>';
                        directionLabel = 'Stabil';
                        directionColor = 'text-gray-600';
                    }

                    directionBox.className = `text-lg font-bold flex items-center gap-2 ${directionColor}`;
                    directionBox.innerHTML = directionIcon + `<span id="trend-direction-text">${directionLabel}</span>`;

                    // Update alert box based on severity
                    let bgClass = 'bg-blue-50 border-blue-200';
                    let iconColor = 'text-blue-500';
                    let textColor = 'text-blue-800';
                    let descColor = 'text-blue-700';
                    let iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    let emoji = 'ℹ️';

                    if (analysis.severity === 'danger') {
                        bgClass = 'bg-red-50 border-red-200';
                        iconColor = 'text-red-500';
                        textColor = 'text-red-800';
                        descColor = 'text-red-700';
                        iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
                        emoji = '🚨';
                    } else if (analysis.severity === 'warning') {
                        bgClass = 'bg-amber-50 border-amber-200';
                        iconColor = 'text-amber-500';
                        textColor = 'text-amber-800';
                        descColor = 'text-amber-700';
                        iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        emoji = '⚠️';
                    } else if (analysis.severity === 'success') {
                        bgClass = 'bg-emerald-50 border-emerald-200';
                        iconColor = 'text-emerald-500';
                        textColor = 'text-emerald-800';
                        descColor = 'text-emerald-700';
                        iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        emoji = '✅';
                    }

                    alertBox.className = `mb-6 p-4 rounded-xl border-2 ${bgClass} flex items-start gap-4 transition-all duration-500`;
                    alertIcon.className = `w-8 h-8 ${iconColor}`;
                    alertIcon.innerHTML = iconSvg;
                    alertTitle.className = `font-bold ${textColor} text-lg mb-1`;
                    alertTitle.textContent = `${emoji} ${analysis.alert_message}`;
                    alertDesc.className = `text-sm ${descColor} opacity-80`;
                    alertDesc.innerHTML = `<strong>${analysis.severity === 'danger' ? 'KRITIS' : analysis.severity === 'warning' ? 'PERINGATAN' : analysis.severity === 'success' ? 'MEMBAIK' : 'INFO'}:</strong> ${analysis.recommendation}`;
                    changePercent.className = `text-3xl font-black ${textColor}`;
                }

                function updateAnalysisModule(vibrationData) {
                    if (!vibrationData || vibrationData.length === 0) return;

                    const values = vibrationData.map(v => v.y);
                    const currentValue = values[values.length - 1];
                    const avgValue = values.reduce((a, b) => a + b, 0) / values.length;
                    const maxValue = Math.max(...values);

                    // Update UI Numbers (3 decimal places for precision)
                    document.getElementById('analysis-rms-current').textContent = currentValue.toFixed(3);
                    document.getElementById('analysis-rms-avg').textContent = avgValue.toFixed(3);
                    document.getElementById('analysis-rms-max').textContent = maxValue.toFixed(3);

                    // Determine Status (ISO 10816-3 Thresholds for Medium Machines)
                    let status = "NORMAL";
                    let interpretation = "";
                    let colorClass = "emerald";
                    let iconPath = "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"; // Checkmark

                    if (currentValue >= 7.1) {
                        status = "DANGER";
                        interpretation = `Nilai RMS sebesar ${currentValue.toFixed(2)} mm/s berada pada kategori Danger. Sangat tinggi, segera lakukan inspeksi mendalam!`;
                        colorClass = "red";
                        iconPath = "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"; // Warning Triangle
                    } else if (currentValue >= 2.8) {
                        status = "WARNING";
                        interpretation = `Nilai RMS sebesar ${currentValue.toFixed(2)} mm/s berada pada kategori Warning. Terdeteksi peningkatan getaran yang tidak wajar.`;
                        colorClass = "orange";
                        iconPath = "M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"; // Exclamation
                    } else {
                        status = "NORMAL";
                        interpretation = `Nilai RMS sebesar ${currentValue.toFixed(2)} mm/s dalam kondisi Normal (ISO 10816-3). Kondisi mesin stabil.`;
                        colorClass = "emerald";
                        iconPath = "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z";
                    }

                    // Update Status UI
                    const statusText = document.getElementById('status-text');
                    const statusInterp = document.getElementById('status-interpretation');
                    const statusIndicator = document.getElementById('status-indicator');
                    const statusIcon = document.getElementById('status-icon');
                    const statusBg = document.getElementById('status-bg');
                    const statusLabel = document.getElementById('status-label');
                    const interpBox = document.getElementById('interpretation-box');
                    const isoDot = document.getElementById('iso-dot');

                    statusText.textContent = status;
                    statusInterp.textContent = interpretation;

                    // HIGH CONTRAST REFINEMENT:
                    // Color the text and icons, keep background mostly white for maximum readability
                    statusText.className = `text-2xl font-black uppercase tracking-tight mb-3 text-${colorClass}-600`;
                    statusLabel.className = `text-[10px] font-black uppercase tracking-[0.2em] mb-4 text-${colorClass}-500/70`;

                    statusIndicator.className = `w-16 h-16 rounded-full border-4 flex items-center justify-center mb-4 transition-all duration-500 relative shadow-sm border-${colorClass}-100 bg-${colorClass}-50/50`;
                    statusIcon.className = `w-8 h-8 text-${colorClass}-500 transition-colors`;
                    statusIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />`;

                    // Box Interpretation: Solid dark text (slate-800) for maximum clarity
                    interpBox.className = `px-3 py-2.5 rounded-xl border w-full transition-colors duration-500 bg-${colorClass}-50/40 border-${colorClass}-100/50`;
                    statusInterp.className = `text-[11px] text-slate-800 font-bold leading-tight`;

                    // Background Accent: Very subtle glow
                    statusBg.className = `absolute inset-0 opacity-[0.05] transition-opacity duration-500 bg-gradient-to-br from-${colorClass}-600 to-transparent`;

                    isoDot.className = `w-1.5 h-1.5 rounded-full bg-${colorClass}-400`;
                }

                function updateFFTDiagnostic(fftData) {
                    if (!fftData || fftData.length === 0) return;

                    // 1. Find the Peak Frequency & Amplitude
                    let maxAmp = 0;
                    let dominantFreq = 0;
                    fftData.forEach(p => {
                        if (p.y > maxAmp) {
                            maxAmp = p.y;
                            dominantFreq = p.x;
                        }
                    });

                    // Update UI Numbers
                    document.getElementById('diagnostic-freq').textContent = dominantFreq.toFixed(1);
                    document.getElementById('diagnostic-amp').textContent = maxAmp.toFixed(3);
                    document.getElementById('dominant-freq').textContent = dominantFreq.toFixed(1);

                    // 2. Logic Diagnosa (Simplified Machine Learning Heuristic)
                    // Assumption: Machine Base RPM is approx 1450-1500 RPM (~24-25 Hz)
                    // This is for demonstration, real logic usually needs RPM sensor input
                    const machineRPM_Hz = 24.8; // Example: 1488 RPM

                    let indicators = {
                        unbalance: false,
                        misalignment: false,
                        bearing: false,
                        looseness: false
                    };

                    let verdict = "Kondisi mesin terpantau optimal tanpa indikasi kerusakan frekuensi yang signifikan.";
                    let statusColor = "emerald";

                    // Threshold amplitude for diagnostic concern (e.g., > 0.5 mm/s at specific peak)
                    if (maxAmp > 0.3) {
                        // Check Unbalance (Large 1x RPM)
                        if (dominantFreq >= machineRPM_Hz - 2 && dominantFreq <= machineRPM_Hz + 2) {
                            indicators.unbalance = true;
                            verdict = `Puncak dominan pada ${dominantFreq.toFixed(1)} Hz (1x RPM) mengindikasikan kemungkinan Unbalance Rotor.`;
                            statusColor = "red";
                        }
                        // Check Misalignment (Large 2x RPM)
                        else if (dominantFreq >= (machineRPM_Hz * 2) - 3 && dominantFreq <= (machineRPM_Hz * 2) + 3) {
                            indicators.misalignment = true;
                            verdict = `Terdeteksi puncak tinggi pada ${dominantFreq.toFixed(1)} Hz (2x RPM), menunjukkan potensi Misalignment pada coupling/shaft.`;
                            statusColor = "orange";
                        }
                        // Check Looseness (Harmonics or lower freq)
                        else if (dominantFreq < machineRPM_Hz - 5) {
                            indicators.looseness = true;
                            verdict = `Getaran frekuensi rendah pada ${dominantFreq.toFixed(1)} Hz kemungkinan disebabkan oleh Mechanical Looseness atau fondasi longgar.`;
                            statusColor = "yellow";
                        }
                        // Check Bearing Defect (High Freq Peaks)
                        else if (dominantFreq > 100) {
                            indicators.bearing = true;
                            verdict = `Puncak frekuensi tinggi pada ${dominantFreq.toFixed(1)} Hz terdeteksi. Ini adalah karakteristik awal kerusakan pada Roller Bearing.`;
                            statusColor = "blue";
                        }
                    }

                    // 3. Update UI Indicators
                    const resetIndicator = (id) => {
                        const el = document.getElementById(id);
                        el.classList.add('grayscale', 'opacity-40');
                        el.classList.remove('scale-110');
                    };

                    const setActiveIndicator = (id) => {
                        const el = document.getElementById(id);
                        el.classList.remove('grayscale', 'opacity-40');
                        el.classList.add('scale-110');
                    };

                    resetIndicator('indicator-unbalance');
                    resetIndicator('indicator-misalignment');
                    resetIndicator('indicator-bearing');
                    resetIndicator('indicator-looseness');

                    if (indicators.unbalance) setActiveIndicator('indicator-unbalance');
                    if (indicators.misalignment) setActiveIndicator('indicator-misalignment');
                    if (indicators.bearing) setActiveIndicator('indicator-bearing');
                    if (indicators.looseness) setActiveIndicator('indicator-looseness');

                    // Update Verdict & Glow
                    const verdictEl = document.getElementById('diagnostic-verdict');
                    const glowEl = document.getElementById('diagnostic-glow');

                    verdictEl.textContent = `"${verdict}"`;
                    verdictEl.className = `text-sm font-bold leading-tight transition-colors duration-500 text-${statusColor}-700`;
                    glowEl.className = `absolute -right-20 -bottom-20 w-64 h-64 rounded-full blur-3xl opacity-50 transition-colors duration-500 bg-${statusColor}-50`;
                }

                function updateClock() {
                    const now = new Date();
                    const clockEl = document.getElementById('currentTime');
                    if (clockEl) {
                        clockEl.textContent = now.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        }).replace(/\./g, ':');
                    }
                }

                document.addEventListener('DOMContentLoaded', () => {
                    initCharts();
                    updateClock();
                    setInterval(updateClock, 1000);
                });
            </script>
        @endpush


        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out forwards;
            }
        </style>
</x-app-layout>
