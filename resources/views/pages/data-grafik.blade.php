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
                    <button id="switch-graph" class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 bg-white text-emerald-600 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Grafik
                    </button>
                    <button id="switch-analysis" class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 text-gray-500 hover:text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analisis
                    </button>
                </div>
            </div>

            <!-- Modul Grafik -->
            <div id="graph-module" class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-3">Grafik</h2>
                <canvas id="graphCanvas"></canvas>
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
</x-app-layout>