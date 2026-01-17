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
            <div
                class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-8 uppercase tracking-tight relative overflow-hidden">
                <!-- Top Accent Bar -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-emerald-500/80"></div>

                <!-- Header "Filters" -->
                <div class="flex items-center space-x-2.5 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-1.5 bg-emerald-50 rounded-lg">
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
                            <select id="filter-machine" onchange="applyFilter()"
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
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
                            <select id="filter-axis" onchange="applyFilter()"
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
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
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
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
                        <div class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col rounded-xl h-[520px]">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Time Domain</h3>
                                    <p class="text-[12px] text-gray-500 font-medium">Visualisasi amplitudo getaran (RMS)
                                        dan
                                        suhu terhadap waktu</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <!-- Reset Zoom Button -->
                                    <button onclick="resetZoom()"
                                        class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all title='Reset Zoom'">
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
                </div>

                <!-- Frequency Domain Analysis Module (FFT) -->
                <div
                    class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col rounded-xl h-[520px] animate-fade-in">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Frequency Domain (FFT)
                            </h3>
                            <p class="text-[12px] text-gray-500 font-medium">Visualisasi spektrum frekuensi hasil Fast
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
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all title='Reset Zoom'">
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
                                <p class="text-gray-400 mt-1 uppercase tracking-widest" style="font-size: 9px;">Spectrum
                                    akan diupdate otomatis</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer: Frequency Bands -->
                    <div class="mt-4 flex flex-wrap items-center gap-4 pt-3 border-t border-gray-50">
                        <div class="flex items-center space-x-1.5">
                            <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                            <span class="font-black text-gray-400 uppercase tracking-tight" style="font-size: 9px;">Low
                                (0-100Hz)</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            <span class="font-black text-gray-400 uppercase tracking-tight" style="font-size: 9px;">Mid
                                (100-500Hz)</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                            <span class="font-black text-gray-400 uppercase tracking-tight" style="font-size: 9px;">High
                                (500Hz+)</span>
                        </div>
                        <div class="flex-grow"></div>
                        <p class="text-gray-400/70 italic" style="font-size: 9px;">Dianalisis dari batch sensor terbaru
                        </p>
                    </div>
                </div>

                <!-- Long-term Trend Analysis Module (OPSIONAL tapi KUAT) -->
                <div
                    class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col rounded-xl h-[520px] animate-fade-in">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Tren Kondisi (Riwayat)
                            </h3>
                            <p class="text-[12px] text-gray-500 font-medium">Monitoring tren degradasi mesin melalui
                                Moving
                                Average RMS harian</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Trend View Toggles -->
                            <div class="flex bg-gray-50 p-1 rounded-lg">
                                <button onclick="setTrendPeriod('daily')" id="btn-trend-daily"
                                    class="px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-emerald-600 transition-all uppercase tracking-widest">Harian</button>
                                <button onclick="setTrendPeriod('weekly')" id="btn-trend-weekly"
                                    class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-400 hover:text-emerald-500 transition-all uppercase tracking-widest">Mingguan</button>
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
                            class="absolute inset-0 flex items-center justify-center bg-gray-50/50 rounded-xl border border-dashed border-gray-200 z-10 transition-opacity">
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
                            <span class="font-black text-gray-400 uppercase tracking-tight" style="font-size: 9px;">Avg
                                RMS</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.4)]">
                            </div>
                            <span class="font-black text-gray-400 uppercase tracking-tight" style="font-size: 9px;">SMA
                                7-Day</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-2 h-2 rounded-lg bg-orange-200 border border-orange-400"></div>
                            <span class="font-black text-gray-400 uppercase tracking-tight" style="font-size: 9px;">Max
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
                            }

                            // Load Trend Data
                            fetchTrendData();
                        }
                    } catch (error) {
                        console.error('Error fetching data:', error);
                    }
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