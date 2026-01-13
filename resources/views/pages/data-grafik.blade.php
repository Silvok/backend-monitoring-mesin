<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl" style="color: #185519;">
                    Monitoring Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full border"
                    style="background-color: #f0faf3; border-color: #b3e5c0;">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                            style="background-color: #2bc970;"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3"
                            style="background-color: #118B50;"></span>
                    </div>
                    <span class="text-xs font-semibold" style="color: #185519;">Live</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold"
                        id="currentTime">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
                </div>
                <button onclick="refreshDashboard()" aria-label="Refresh Data Grafik"
                    class="px-4 py-1.5 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
                    style="background-color: #118B50;" onmouseover="this.style.backgroundColor='#185519'"
                    onmouseout="this.style.backgroundColor='#118B50'">
                    <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <!-- Filter Form: Tanggal & Mesin -->
            <!-- Filter Global -->
            <div class="bg-white rounded-lg mb-8 shadow-lg border border-gray-100 overflow-hidden">
                <!-- Green Top Border -->
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #10b981 0%, #34d399 100%);"></div>
                <div class="px-5 py-4">
                    <!-- Header with Icon -->
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg"
                            style="background-color: #ecfdf5;">
                            <svg class="w-5 h-5" style="color: #10b981;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-800">Filter Global</h2>
                    </div>

                    <form class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <!-- Mesin / Node ESP -->
                        <div>
                            <label for="machine" class="block text-sm font-medium text-gray-600 mb-2">Mesin / Node
                                ESP</label>
                            <select id="machine" name="machine"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                                <option value="">Pilih Mesin</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rentang Waktu -->
                        <div>
                            <label for="time-range" class="block text-sm font-medium text-gray-600 mb-2">Rentang
                                Waktu</label>
                            <select id="time-range" name="time-range"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                                <option value="real-time">Real-time</option>
                                <option value="1-hour">1 jam terakhir</option>
                                <option value="24-hours">24 jam</option>
                                <option value="custom">Custom range</option>
                            </select>
                        </div>

                        <!-- Custom Date Range (Hidden by default) -->
                        <div id="custom-date-range" class="hidden lg:col-span-2 grid grid-cols-2 gap-3">
                            <div>
                                <label for="date-start" class="block text-sm font-medium text-gray-600 mb-2">Tanggal
                                    Mulai</label>
                                <input type="datetime-local" id="date-start" name="date-start"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                            </div>
                            <div>
                                <label for="date-end" class="block text-sm font-medium text-gray-600 mb-2">Tanggal
                                    Akhir</label>
                                <input type="datetime-local" id="date-end" name="date-end"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                            </div>
                        </div>

                        <!-- Axis Getaran -->
                        <div>
                            <label for="axis" class="block text-sm font-medium text-gray-600 mb-2">Axis Getaran</label>
                            <select id="axis" name="axis"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                                <option value="x">X</option>
                                <option value="y">Y</option>
                                <option value="z">Z</option>
                                <option value="resultant">Resultant</option>
                            </select>
                        </div>

                        <!-- Jenis Data -->
                        <div>
                            <label for="data-type" class="block text-sm font-medium text-gray-600 mb-2">Jenis
                                Data</label>
                            <select id="data-type" name="data-type"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                                <option value="raw">Raw signal</option>
                                <option value="rms">RMS</option>
                                <option value="fft">FFT</option>
                            </select>
                        </div>

                        <!-- Sampling Window -->
                        <div>
                            <label for="sampling-window" class="block text-sm font-medium text-gray-600 mb-2">Sampling
                                Window</label>
                            <select id="sampling-window" name="sampling-window"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:bg-white hover:border-gray-300">
                                <option value="1s">1s</option>
                                <option value="5s">5s</option>
                                <option value="10s">10s</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Button Switch Modul -->
            <div class="flex justify-center mb-6">
                <div class="inline-flex bg-gray-100 rounded-full p-1">
                    <button id="switch-graph"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-emerald-600 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                        Grafik
                    </button>
                    <button id="switch-analysis"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 text-gray-500 hover:text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Analisis
                    </button>
                </div>
            </div>

            <!-- Modul Grafik -->
            <div id="graph-module" class="bg-white shadow -lg rounded-lg overflow-hidden border border-gray-100">
                <!-- Sub-tabs untuk jenis grafik -->
                <div class="border-b border-gray-200 bg-gray-50 px-4">
                    <nav class="flex space-x-1" aria-label="Tabs">
                        <button id="tab-time-domain" class="px-4 py-3 text-sm font-medium border-b-2 border-emerald-500 text-emerald-600">
                            Time Domain
                        </button>
                        <button id="tab-fft" class="px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            FFT / Frequency
                        </button>
                        <button id="tab-history" class="px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Riwayat & Trend
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Time Domain Panel -->
                    <div id="panel-time-domain">
                        <!-- RMS Getaran Chart -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">RMS Getaran vs Waktu</h3>
                                    <p class="text-sm text-gray-500">Visualisasi nilai RMS getaran mesin dalam domain waktu</p>
                                </div>
                                <!-- Axis Toggle -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-500">Axis:</span>
                                    <div class="inline-flex bg-gray-100 rounded-lg p-1">
                                        <button data-axis="x" class="axis-toggle px-3 py-1 text-xs font-medium rounded-md bg-white text-emerald-600 shadow-sm">X</button>
                                        <button data-axis="y" class="axis-toggle px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">Y</button>
                                        <button data-axis="z" class="axis-toggle px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">Z</button>
                                        <button data-axis="resultant" class="axis-toggle px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">Resultant</button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative bg-gray-50 rounded-lg p-4 border border-gray-200" style="height: 320px;">
                                <canvas id="rmsTimeDomainChart"></canvas>
                            </div>
                            <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                <span>Gunakan scroll untuk zoom, drag untuk pan</span>
                                <button id="reset-zoom-rms" class="text-emerald-600 hover:text-emerald-700 font-medium">Reset Zoom</button>
                            </div>
                        </div>

                        <!-- Temperature Chart -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Suhu vs Waktu</h3>
                                    <p class="text-sm text-gray-500">Visualisasi perubahan suhu mesin dalam domain waktu</p>
                                </div>
                            </div>
                            <div class="relative bg-gray-50 rounded-lg p-4 border border-gray-200" style="height: 240px;">
                                <canvas id="temperatureChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- FFT / Frequency Domain Panel -->
                    <div id="panel-fft" class="hidden">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Spektrum Frekuensi (FFT)</h3>
                                    <p class="text-sm text-gray-500">Analisis frekuensi menggunakan Fast Fourier Transform</p>
                                </div>
                                <!-- Band Frequency Selector -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-500">Band:</span>
                                    <div class="inline-flex bg-gray-100 rounded-lg p-1">
                                        <button data-band="all" class="band-toggle px-3 py-1 text-xs font-medium rounded-md bg-white text-emerald-600 shadow-sm">All</button>
                                        <button data-band="low" class="band-toggle px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">Low (0-100Hz)</button>
                                        <button data-band="mid" class="band-toggle px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">Mid (100-500Hz)</button>
                                        <button data-band="high" class="band-toggle px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">High (>500Hz)</button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative bg-gray-50 rounded-lg p-4 border border-gray-200" style="height: 350px;">
                                <canvas id="fftChart"></canvas>
                            </div>
                            <!-- FFT Info Cards -->
                            <div class="grid grid-cols-3 gap-4 mt-4">
                                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4 border border-emerald-200">
                                    <p class="text-xs text-emerald-600 font-medium mb-1">Frekuensi Dominan</p>
                                    <p id="dominant-freq" class="text-xl font-bold text-emerald-800">-- Hz</p>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                    <p class="text-xs text-blue-600 font-medium mb-1">Amplitudo Peak</p>
                                    <p id="peak-amp" class="text-xl font-bold text-blue-800">-- g</p>
                                </div>
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                                    <p class="text-xs text-purple-600 font-medium mb-1">Jumlah Peak Terdeteksi</p>
                                    <p id="peak-count" class="text-xl font-bold text-purple-800">--</p>
                                </div>
                            </div>
                        </div>

                        <!-- Time vs Frequency Comparison -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mt-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-amber-800">Perbedaan Time Domain vs Frequency Domain</h4>
                                    <p class="text-sm text-amber-700 mt-1">
                                        <strong>Time Domain:</strong> Menampilkan bagaimana getaran berubah seiring waktu (kapan terjadi).
                                        <strong>Frequency Domain:</strong> Menampilkan komponen frekuensi getaran (apa yang terjadi) - membantu mengidentifikasi masalah seperti unbalance, misalignment, atau bearing defects.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History & Trend Panel -->
                    <div id="panel-history" class="hidden">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Trend RMS Harian/Mingguan</h3>
                                    <p class="text-sm text-gray-500">Visualisasi trend getaran untuk deteksi degradasi mesin</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-500">Periode:</span>
                                    <select id="trend-period" class="text-xs bg-gray-100 border-0 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500">
                                        <option value="7">7 Hari</option>
                                        <option value="14">14 Hari</option>
                                        <option value="30">30 Hari</option>
                                    </select>
                                </div>
                            </div>
                            <div class="relative bg-gray-50 rounded-lg p-4 border border-gray-200" style="height: 300px;">
                                <canvas id="trendChart"></canvas>
                            </div>
                            <div class="flex items-center gap-4 mt-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <span class="text-gray-600">RMS Harian</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-1 rounded-full bg-orange-500"></span>
                                    <span class="text-gray-600">Moving Average (3 hari)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modul Analisis -->
            <div id="analysis-module" class="bg-white shadow rounded-lg p-4 hidden">
                <h2 class="text-lg font-semibold mb-3">Analisis</h2>
                <p>Hasil analisis akan ditampilkan di sini.</p>
            </div>
        </div>
    </div>

    <script>
        const btnGraph = document.getElementById('switch-graph');
        const btnAnalysis = document.getElementById('switch-analysis');
        const activeClasses = ['bg-white', 'text-emerald-600', 'shadow-sm'];
        const inactiveClasses = ['text-gray-500', 'hover:text-gray-700'];

        // Switch between modules
        btnGraph.addEventListener('click', function () {
            document.getElementById('graph-module').classList.remove('hidden');
            document.getElementById('analysis-module').classList.add('hidden');
            // Update button styles
            btnGraph.classList.add(...activeClasses);
            btnGraph.classList.remove(...inactiveClasses);
            btnAnalysis.classList.remove(...activeClasses);
            btnAnalysis.classList.add(...inactiveClasses);
        });

        btnAnalysis.addEventListener('click', function () {
            document.getElementById('analysis-module').classList.remove('hidden');
            document.getElementById('graph-module').classList.add('hidden');
            // Update button styles
            btnAnalysis.classList.add(...activeClasses);
            btnAnalysis.classList.remove(...inactiveClasses);
            btnGraph.classList.remove(...activeClasses);
            btnGraph.classList.add(...inactiveClasses);
        });

        // Toggle Custom Date Range
        document.getElementById('time-range').addEventListener('change', function () {
            const customDateRange = document.getElementById('custom-date-range');
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
            } else {
                customDateRange.classList.add('hidden');
            }
        });
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>

    <script>
        // Data dari controller
        const rmsChartData = @json($rmsChartData ?? ['labels' => [], 'values' => []]);

        // =====================
        // Graph Sub-Tab Navigation
        // =====================
        const tabs = {
            'tab-time-domain': 'panel-time-domain',
            'tab-fft': 'panel-fft',
            'tab-history': 'panel-history'
        };

        Object.keys(tabs).forEach(tabId => {
            document.getElementById(tabId)?.addEventListener('click', function() {
                // Hide all panels
                Object.values(tabs).forEach(panelId => {
                    document.getElementById(panelId)?.classList.add('hidden');
                });
                // Remove active state from all tabs
                Object.keys(tabs).forEach(t => {
                    const tab = document.getElementById(t);
                    tab?.classList.remove('border-emerald-500', 'text-emerald-600');
                    tab?.classList.add('border-transparent', 'text-gray-500');
                });
                // Show selected panel
                document.getElementById(tabs[tabId])?.classList.remove('hidden');
                // Set active state
                this.classList.add('border-emerald-500', 'text-emerald-600');
                this.classList.remove('border-transparent', 'text-gray-500');
            });
        });

        // =====================
        // Time Domain Chart - RMS
        // =====================
        let rmsChart = null;
        const rmsCtx = document.getElementById('rmsTimeDomainChart');

        if (rmsCtx) {
            rmsChart = new Chart(rmsCtx, {
                type: 'line',
                data: {
                    labels: rmsChartData.labels || [],
                    datasets: [{
                        label: 'RMS Getaran (g)',
                        data: rmsChartData.values || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointHoverBackgroundColor: '#059669'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    const idx = context[0].dataIndex;
                                    return rmsChartData.full_times?.[idx] || context[0].label;
                                },
                                label: function(context) {
                                    return `RMS: ${context.parsed.y?.toFixed(4) || 0} g`;
                                },
                                afterLabel: function(context) {
                                    const idx = context.dataIndex;
                                    const machine = rmsChartData.machines?.[idx] || '-';
                                    const status = rmsChartData.statuses?.[idx] || '-';
                                    return `Mesin: ${machine}\nStatus: ${status}`;
                                }
                            }
                        },
                        zoom: {
                            zoom: {
                                wheel: { enabled: true },
                                pinch: { enabled: true },
                                mode: 'xy',
                            },
                            pan: {
                                enabled: true,
                                mode: 'xy',
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { maxRotation: 45, font: { size: 10 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: { display: true, text: 'RMS (g)', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        // Reset Zoom Button
        document.getElementById('reset-zoom-rms')?.addEventListener('click', function() {
            if (rmsChart) rmsChart.resetZoom();
        });

        // =====================
        // Temperature Chart
        // =====================
        let tempChart = null;
        const tempCtx = document.getElementById('temperatureChart');

        if (tempCtx) {
            // Generate sample temperature data (would come from actual data in production)
            const tempLabels = rmsChartData.labels || [];
            const tempData = tempLabels.map(() => 25 + Math.random() * 15);

            tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: tempLabels,
                    datasets: [{
                        label: 'Suhu (°C)',
                        data: tempData,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#f59e0b'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    return `Suhu: ${context.parsed.y?.toFixed(1) || 0} °C`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { maxRotation: 45, font: { size: 10 } }
                        },
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: { display: true, text: 'Suhu (°C)', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        // =====================
        // FFT Chart
        // =====================
        let fftChart = null;
        const fftCtx = document.getElementById('fftChart');

        if (fftCtx) {
            // Generate sample FFT data
            const fftFrequencies = Array.from({length: 100}, (_, i) => i * 10); // 0-990 Hz
            const fftAmplitudes = fftFrequencies.map(f => {
                // Simulate some peaks
                if (f === 120) return 0.8;
                if (f === 240) return 0.45;
                if (f === 360) return 0.25;
                return Math.random() * 0.1;
            });

            // Find peaks
            const peaks = [];
            for (let i = 1; i < fftAmplitudes.length - 1; i++) {
                if (fftAmplitudes[i] > fftAmplitudes[i-1] && fftAmplitudes[i] > fftAmplitudes[i+1] && fftAmplitudes[i] > 0.2) {
                    peaks.push({ freq: fftFrequencies[i], amp: fftAmplitudes[i] });
                }
            }

            // Update info cards
            if (peaks.length > 0) {
                const maxPeak = peaks.reduce((a, b) => a.amp > b.amp ? a : b);
                document.getElementById('dominant-freq').textContent = maxPeak.freq + ' Hz';
                document.getElementById('peak-amp').textContent = maxPeak.amp.toFixed(3) + ' g';
            }
            document.getElementById('peak-count').textContent = peaks.length;

            fftChart = new Chart(fftCtx, {
                type: 'bar',
                data: {
                    labels: fftFrequencies.map(f => f + ' Hz'),
                    datasets: [{
                        label: 'Amplitudo (g)',
                        data: fftAmplitudes,
                        backgroundColor: fftFrequencies.map(f => {
                            if (f < 100) return 'rgba(34, 197, 94, 0.7)'; // Low - green
                            if (f < 500) return 'rgba(59, 130, 246, 0.7)'; // Mid - blue
                            return 'rgba(168, 85, 247, 0.7)'; // High - purple
                        }),
                        borderColor: fftFrequencies.map(f => {
                            if (f < 100) return '#22c55e';
                            if (f < 500) return '#3b82f6';
                            return '#a855f7';
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                title: function(context) {
                                    return `Frekuensi: ${context[0].label}`;
                                },
                                label: function(context) {
                                    return `Amplitudo: ${context.parsed.y?.toFixed(4) || 0} g`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                maxRotation: 0, 
                                font: { size: 9 },
                                callback: function(value, index) {
                                    return index % 10 === 0 ? this.getLabelForValue(value) : '';
                                }
                            },
                            title: { display: true, text: 'Frekuensi (Hz)', font: { size: 12 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: { display: true, text: 'Amplitudo (g)', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        // Band Toggle for FFT
        document.querySelectorAll('.band-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update button styles
                document.querySelectorAll('.band-toggle').forEach(b => {
                    b.classList.remove('bg-white', 'text-emerald-600', 'shadow-sm');
                    b.classList.add('text-gray-500');
                });
                this.classList.add('bg-white', 'text-emerald-600', 'shadow-sm');
                this.classList.remove('text-gray-500');

                // Filter chart based on band
                const band = this.dataset.band;
                if (fftChart) {
                    const allFreqs = Array.from({length: 100}, (_, i) => i * 10);
                    let filteredIndices;
                    
                    switch(band) {
                        case 'low':
                            filteredIndices = allFreqs.map((f, i) => f < 100 ? i : null).filter(i => i !== null);
                            break;
                        case 'mid':
                            filteredIndices = allFreqs.map((f, i) => (f >= 100 && f < 500) ? i : null).filter(i => i !== null);
                            break;
                        case 'high':
                            filteredIndices = allFreqs.map((f, i) => f >= 500 ? i : null).filter(i => i !== null);
                            break;
                        default:
                            filteredIndices = allFreqs.map((f, i) => i);
                    }
                    
                    // Update chart visibility
                    fftChart.data.datasets[0].data = allFreqs.map((f, i) => {
                        if (band === 'all') return fftChart.data.datasets[0].data[i];
                        if (band === 'low' && f < 100) return fftChart.data.datasets[0].data[i];
                        if (band === 'mid' && f >= 100 && f < 500) return fftChart.data.datasets[0].data[i];
                        if (band === 'high' && f >= 500) return fftChart.data.datasets[0].data[i];
                        return 0;
                    });
                    fftChart.update();
                }
            });
        });

        // =====================
        // Trend Chart
        // =====================
        let trendChart = null;
        const trendCtx = document.getElementById('trendChart');

        if (trendCtx) {
            // Generate sample trend data
            const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            const trendData = [0.35, 0.38, 0.36, 0.42, 0.45, 0.43, 0.48];
            
            // Calculate moving average
            const movingAvg = trendData.map((val, i, arr) => {
                if (i < 2) return null;
                return (arr[i-2] + arr[i-1] + val) / 3;
            });

            trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [
                        {
                            label: 'RMS Harian',
                            data: trendData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 5,
                            pointBackgroundColor: '#10b981'
                        },
                        {
                            label: 'Moving Average (3 hari)',
                            data: movingAvg,
                            borderColor: '#f97316',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y?.toFixed(3) || 0} g`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: { display: true, text: 'RMS (g)', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        // =====================
        // Axis Toggle for Time Domain
        // =====================
        document.querySelectorAll('.axis-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update button styles
                document.querySelectorAll('.axis-toggle').forEach(b => {
                    b.classList.remove('bg-white', 'text-emerald-600', 'shadow-sm');
                    b.classList.add('text-gray-500');
                });
                this.classList.add('bg-white', 'text-emerald-600', 'shadow-sm');
                this.classList.remove('text-gray-500');

                // Update chart color based on axis
                const axis = this.dataset.axis;
                const colors = {
                    'x': { border: '#ef4444', bg: 'rgba(239, 68, 68, 0.1)' },
                    'y': { border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.1)' },
                    'z': { border: '#8b5cf6', bg: 'rgba(139, 92, 246, 0.1)' },
                    'resultant': { border: '#10b981', bg: 'rgba(16, 185, 129, 0.1)' }
                };

                if (rmsChart) {
                    rmsChart.data.datasets[0].borderColor = colors[axis].border;
                    rmsChart.data.datasets[0].backgroundColor = colors[axis].bg;
                    rmsChart.data.datasets[0].pointBackgroundColor = colors[axis].border;
                    rmsChart.data.datasets[0].label = `RMS Axis ${axis.toUpperCase()} (g)`;
                    rmsChart.update();
                }
            });
        });
    </script>
</x-app-layout>