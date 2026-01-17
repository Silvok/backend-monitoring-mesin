<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endpush
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h2 class="font-bold text-2xl text-emerald-900 tracking-tight">
                    Monitoring & Analisis Mesin
                </h2>
            </div>

            <!-- Module Switcher -->
            <div class="flex bg-gray-100 p-1.5 rounded-xl border border-gray-200 shadow-sm">
                <button onclick="switchModule('grafik')" id="btn-grafik"
                    class="flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 bg-white text-emerald-600 shadow-sm border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span>Grafik</span>
                </button>
                <button onclick="switchModule('analisis')" id="btn-analisis"
                    class="flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 text-gray-500 hover:text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span>Analisis</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Global Filter Card -->
            <div class="bg-white shadow-sm border border-emerald-100 overflow-hidden" style="border-radius: 1rem;">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-3 border-b border-emerald-100"
                    style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider">Global Filter</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <!-- Machine Selector -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Mesin / Node
                                ESP</label>
                            <select id="filter-machine"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Time Range -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Rentang
                                Waktu</label>
                            <select id="filter-time-range" onchange="toggleCustomRange()"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="realtime">Real-time</option>
                                <option value="1h">1 Jam Terakhir</option>
                                <option value="24h" selected>24 Jam Terakhir</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>

                        <!-- Vibration Axis -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Axis
                                Getaran</label>
                            <select id="filter-axis"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="x">Sumbu X</option>
                                <option value="y">Sumbu Y</option>
                                <option value="z">Sumbu Z</option>
                                <option value="resultant" selected>Resultant (Total)</option>
                            </select>
                        </div>

                        <!-- Data Type -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Jenis
                                Data</label>
                            <select id="filter-data-type"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="raw">Raw Signal</option>
                                <option value="rms" selected>RMS</option>
                                <option value="fft">FFT</option>
                            </select>
                        </div>

                        <!-- Sampling Window -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Sampling
                                Window</label>
                            <select id="filter-window"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="1s">1 Detik</option>
                                <option value="5s" selected>5 Detik</option>
                                <option value="10s">10 Detik</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom Range Inputs (Hidden by default) -->
                    <div id="custom-range-inputs"
                        class="hidden mt-6 pt-6 border-t border-gray-100 flex flex-wrap gap-4 items-end animate-fade-in">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Dari
                                Tanggal</label>
                            <input type="datetime-local" id="filter-start"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Sampai
                                Tanggal</label>
                            <input type="datetime-local" id="filter-end"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <button onclick="applyFilter()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-1.5 px-4 rounded-lg transition-all shadow-sm hover:shadow-md text-xs">
                            Apply Custom Range
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div id="module-container">
                <!-- Grafik Module -->
                <div id="module-grafik" class="space-y-6">
                    <!-- ROW 1: REAL-TIME INSIGHTS -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left: Time Domain Chart -->
                        <div class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col rounded-xl h-[480px]">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Analisis Time Domain</h3>
                                    <p class="text-xs text-gray-500 italic">Visualisasi data getaran dan suhu terhadap
                                        waktu</p>
                                </div>
                                <div class="flex bg-gray-100 p-1 rounded-lg">
                                    <button onclick="toggleDataset(0)" id="btn-rms"
                                        class="px-3 py-1 text-[10px] font-bold rounded-md bg-white shadow-sm text-emerald-600 transition-all">RMS</button>
                                    <button onclick="toggleDataset(1)" id="btn-temp"
                                        class="px-3 py-1 text-[10px] font-bold rounded-md text-gray-500 hover:text-emerald-500 transition-all">SUHU</button>
                                </div>
                            </div>
                            <div class="flex-grow relative overflow-hidden">
                                <canvas id="timeDomainChart"></canvas>
                                <div id="timeDomainPlaceholder"
                                    class="absolute inset-0 flex items-center justify-center bg-gray-50/80 rounded-xl border border-dashed border-gray-200 z-10">
                                    <div class="text-center">
                                        <div class="animate-bounce mb-2">
                                            <svg class="w-8 h-8 text-emerald-400 mx-auto" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-600">Menunggu Inisialisasi Mesin...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Integrated Stats (Ringkasan Sesi) -->
                        <div class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col rounded-xl h-[480px]">
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-50 pb-4 mb-6">Ringkasan
                                Sistem</h3>

                            <div class="flex-grow space-y-5">
                                <!-- Machine Health Status -->
                                <div id="stat-status-container"
                                    class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 transition-colors duration-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">
                                            Status Mesin</p>
                                        <span class="flex h-2 w-2 relative">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span
                                                class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                    </div>
                                    <p id="stat-status" class="text-2xl font-black text-emerald-900 tracking-tight">
                                        SIAGA</p>
                                </div>

                                <!-- Key Metrics Merged -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Puncak (Max)</p>
                                        <p id="stat-rms-max" class="text-xl font-bold text-gray-900">0.000</p>
                                        <p class="text-[9px] text-gray-400">mm/s (RMS)</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Rata-rata</p>
                                        <p id="stat-rms-avg" class="text-xl font-bold text-gray-900">0.000</p>
                                        <p class="text-[9px] text-gray-400">mm/s (AVG)</p>
                                    </div>
                                </div>

                                <!-- Health Gauge Integrated -->
                                <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-[10px] font-bold text-blue-700 uppercase">Health Score</p>
                                        <span id="stat-health" class="text-sm font-black text-blue-900">0%</span>
                                    </div>
                                    <div class="w-full bg-blue-200/50 rounded-full h-2.5 overflow-hidden">
                                        <div id="stat-health-bar"
                                            class="bg-blue-600 h-full rounded-full transition-all duration-700 ease-out"
                                            style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Dominant FFT Integrated to Row 1 to reduce Row 2 clutter -->
                                <div class="p-4 bg-purple-50/50 rounded-xl border border-purple-100">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-bold text-purple-700 uppercase mb-1">Freq.
                                                Dominan</p>
                                            <p id="stat-dominant-freq" class="text-xl font-bold text-gray-900">-- <span
                                                    class="text-xs font-normal">Hz</span></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-bold text-purple-700 uppercase mb-1">Amplitudo
                                            </p>
                                            <p id="stat-peak-amp" class="text-sm font-bold text-gray-900">0.000 <span
                                                    class="font-normal text-xs text-gray-400">g</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Final Recommendation -->
                            <div class="mt-6 pt-4 border-t border-gray-50 text-center">
                                <p id="stat-recommendation" class="text-xs text-gray-500 italic px-2">
                                    "Inisialisasi filter untuk memulai diagnosis otomatis."
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2: DETAILED ANALYSIS -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- FFT Spectral View -->
                        <div class="bg-white shadow-sm border border-gray-100 p-6 rounded-xl flex flex-col h-[400px]">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Spektrum Frekuensi (FFT)</h3>
                                    <p class="text-xs text-gray-500 italic">Identifikasi kerusakan berdasarkan komponen
                                        frekuensi</p>
                                </div>
                                <span
                                    class="px-2 py-1 bg-purple-100 text-purple-700 text-[9px] font-black uppercase rounded tracking-widest">Spectral
                                    View</span>
                            </div>
                            <div class="flex-grow relative">
                                <canvas id="fftChart"></canvas>
                                <div id="fftPlaceholder"
                                    class="absolute inset-0 flex items-center justify-center bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                    <p class="text-xs font-bold text-gray-400">Menunggu Transformasi Fourier...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Historical Prediction Trend -->
                        <div class="bg-white shadow-sm border border-gray-100 p-6 rounded-xl flex flex-col h-[400px]">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Trend Riwayat & Prediksi</h3>
                                    <p class="text-xs text-gray-500 italic">Analisis kecenderungan degradasi mesin</p>
                                </div>
                                <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                                    <button
                                        class="px-3 py-1 text-[9px] font-bold text-gray-400 hover:text-emerald-600 transition">DAILY</button>
                                    <button
                                        class="px-3 py-1 text-[9px] font-bold bg-white text-emerald-600 shadow-sm rounded-md transition">WEEKLY</button>
                                </div>
                            </div>
                            <div class="flex-grow relative">
                                <canvas id="historyChart"></canvas>
                                <div id="historyPlaceholder"
                                    class="absolute inset-0 flex items-center justify-center bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                    <p class="text-xs font-bold text-gray-400">Sinkronisasi Data Historis...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analisis Module (Hidden by default) -->
                <div id="module-analisis" class="hidden space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Analysis Result Cards -->
                        <div
                            class="bg-white p-6 rounded-xl shadow-sm border border-emerald-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-2 bg-emerald-50 rounded-lg">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-400">STATUS</span>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900">No Data</h4>
                            <p class="text-xs text-gray-500 mt-1">Kondisi kesehatan mesin</p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-xl shadow-sm border border-blue-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-400">TREND</span>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900">Stable</h4>
                            <p class="text-xs text-gray-500 mt-1">Prediksi 24 jam ke depan</p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-xl shadow-sm border border-purple-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-2 bg-purple-50 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-400">RUNTIME</span>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900">0h</h4>
                            <p class="text-xs text-gray-500 mt-1">Total operasi mesin hari ini</p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-red-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-2 bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-400">ALERTS</span>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900">0</h4>
                            <p class="text-xs text-gray-500 mt-1">Anomali terdeteksi</p>
                        </div>
                    </div>

                    <!-- Scientific Analysis Section -->
                    <div class="bg-white shadow-sm border border-gray-100 p-8" style="border-radius: 1rem;">
                        <div class="max-w-3xl">
                            <h3 class="text-2xl font-bold text-emerald-900 mb-2">Kesimpulan Ilmiah</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Analisis ini didasarkan pada data getaran yang dikumpulkan dari node ESP-32 menggunakan
                                parameter percepatan (G-Force).
                                Kami menggunakan algoritma pemrosesan sinyal untuk menentukan tingkat kesehatan komponen
                                mesin.
                            </p>

                            <div class="space-y-4">
                                <div
                                    class="flex items-start space-x-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                                    <div
                                        class="mt-1 p-1 bg-white rounded shadow-sm text-emerald-600 font-bold text-xs uppercase">
                                        INFO
                                    </div>
                                    <p class="text-sm text-gray-700">
                                        Filter global yang Anda terapkan memastikan konsistensi antara visualisasi
                                        grafik getaran dan hasil analisis degradasi komponen.
                                    </p>
                                </div>
                                <div
                                    class="flex items-start space-x-4 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                                    <div
                                        class="mt-1 p-1 bg-white rounded shadow-sm text-emerald-600 font-bold text-xs uppercase">
                                        ADVICE
                                    </div>
                                    <p class="text-sm text-emerald-800 font-medium">
                                        Silahkan sesuaikan <strong>axis getaran</strong> untuk melihat anomali spesifik
                                        pada sumbu tertentu yang mungkin mengindikasikan ketidakseimbangan (unbalance)
                                        atau misalignmen.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function switchModule(module) {
                const grafikSection = document.getElementById('module-grafik');
                const analisisSection = document.getElementById('module-analisis');
                const btnGrafik = document.getElementById('btn-grafik');
                const btnAnalisis = document.getElementById('btn-analisis');

                if (module === 'grafik') {
                    grafikSection.classList.remove('hidden');
                    analisisSection.classList.add('hidden');

                    btnGrafik.className = "flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 bg-white text-emerald-600 shadow-sm border border-emerald-100";
                    btnAnalisis.className = "flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 text-gray-500 hover:text-emerald-600";
                } else {
                    grafikSection.classList.add('hidden');
                    analisisSection.classList.remove('hidden');

                    btnAnalisis.className = "flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 bg-white text-emerald-600 shadow-sm border border-emerald-100";
                    btnGrafik.className = "flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 text-gray-500 hover:text-emerald-600";
                }
            }

            function toggleCustomRange() {
                const timeRange = document.getElementById('filter-time-range').value;
                const customRangeDiv = document.getElementById('custom-range-inputs');

                if (timeRange === 'custom') {
                    customRangeDiv.classList.remove('hidden');
                    customRangeDiv.classList.add('flex');
                } else {
                    customRangeDiv.classList.add('hidden');
                    customRangeDiv.classList.remove('flex');
                }
            }

            function applyFilter() {
                const machineId = document.getElementById('filter-machine').value;
                if (!machineId) {
                    alert('Mohon pilih mesin terlebih dahulu.');
                    return;
                }

                // Show loading placeholders
                document.getElementById('timeDomainPlaceholder').classList.remove('hidden');
                document.getElementById('fftPlaceholder').classList.remove('hidden');
                document.getElementById('historyPlaceholder').classList.remove('hidden');

                // Simulation for now (replace with actual API fetch later)
                setTimeout(() => {
                    updateCharts(machineId);
                }, 800);
            }

            let timeChart, fftChartObj, historyChartObj;

            function initCharts() {
                // Time Domain Chart
                const ctxTime = document.getElementById('timeDomainChart').getContext('2d');
                timeChart = new Chart(ctxTime, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'RMS Getaran',
                            data: [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0
                        }, {
                            label: 'Suhu (°C)',
                            data: [],
                            borderColor: '#ef4444',
                            borderWidth: 1.5,
                            borderDash: [5, 5],
                            tension: 0.3,
                            pointRadius: 0,
                            hidden: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
                            y: { beginAtZero: true, ticks: { font: { size: 10 } } }
                        }
                    }
                });

                // FFT Chart
                const ctxFft = document.getElementById('fftChart').getContext('2d');
                fftChartObj = new Chart(ctxFft, {
                    type: 'bar',
                    data: {
                        labels: Array.from({ length: 50 }, (_, i) => i * 2),
                        datasets: [{
                            label: 'Amplitude',
                            data: [],
                            backgroundColor: 'rgba(139, 92, 246, 0.6)',
                            borderColor: '#8b5cf6',
                            borderWidth: 1,
                            barPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: (ctx) => `Freq: ${ctx[0].label} Hz`
                                }
                            }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Frequency (Hz)', font: { size: 10, weight: 'bold' } }, ticks: { font: { size: 9 } } },
                            y: { title: { display: true, text: 'Amp (g)', font: { size: 10, weight: 'bold' } }, beginAtZero: true, ticks: { font: { size: 9 } } }
                        }
                    }
                });

                // History Chart
                const ctxHist = document.getElementById('historyChart').getContext('2d');
                historyChartObj = new Chart(ctxHist, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Avg RMS',
                            data: [0.45, 0.42, 0.48, 0.44, 0.55, 0.52, 0.49],
                            borderColor: '#3b82f6',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                            x: { ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }

            function toggleDataset(index) {
                const isHidden = timeChart.getDatasetMeta(index).hidden;
                timeChart.setDatasetVisibility(index, isHidden);
                timeChart.update();

                const btnRms = document.getElementById('btn-rms');
                const btnTemp = document.getElementById('btn-temp');

                if (index === 0) { // RMS
                    btnRms.className = !isHidden
                        ? "px-3 py-1 text-[10px] font-bold rounded-md bg-white shadow-sm text-emerald-600"
                        : "px-3 py-1 text-[10px] font-bold rounded-md text-gray-500 hover:text-emerald-500";
                } else { // Temp
                    btnTemp.className = !isHidden
                        ? "px-3 py-1 text-[10px] font-bold rounded-md bg-white shadow-sm text-red-600 border border-red-50"
                        : "px-3 py-1 text-[10px] font-bold rounded-md text-gray-500 hover:text-red-500";
                }
            }

            function updateCharts(machineId) {
                // Hide placeholders
                document.getElementById('timeDomainPlaceholder').classList.add('hidden');
                document.getElementById('fftPlaceholder').classList.add('hidden');
                document.getElementById('historyPlaceholder').classList.add('hidden');

                // Simulate data update
                const dataPoints = 40;
                const labels = Array.from({ length: dataPoints }, (_, i) => `${i}s`);
                const rmsValues = Array.from({ length: dataPoints }, () => (Math.random() * 0.4) + 0.15);
                const tempValues = Array.from({ length: dataPoints }, () => 38 + (Math.random() * 5));

                timeChart.data.labels = labels;
                timeChart.data.datasets[0].data = rmsValues;
                timeChart.data.datasets[1].data = tempValues;
                timeChart.update();

                // FFT Update
                const fftData = Array.from({ length: 30 }, (_, i) => {
                    const baseFreq = i * 4;
                    if (baseFreq === 20) return 0.78;
                    if (baseFreq === 40) return 0.32;
                    return Math.random() * 0.12;
                });
                fftChartObj.data.labels = Array.from({ length: 30 }, (_, i) => i * 4);
                fftChartObj.data.datasets[0].data = fftData;
                fftChartObj.update();

                // Stats Update
                const maxRms = Math.max(...rmsValues);
                const avgRms = rmsValues.reduce((a, b) => a + b, 0) / rmsValues.length;
                document.getElementById('stat-rms-max').innerText = maxRms.toFixed(3);
                document.getElementById('stat-rms-avg').innerText = avgRms.toFixed(3);

                const statusContainer = document.getElementById('stat-status-container');
                const statusText = document.getElementById('stat-status');

                if (maxRms > 0.45) {
                    statusText.innerText = 'WASPADA';
                    statusContainer.className = 'p-4 bg-orange-50 rounded-xl border border-orange-100 transition-all';
                    statusText.className = 'text-2xl font-black text-orange-900 tracking-tight';
                } else {
                    statusText.innerText = 'NORMAL';
                    statusContainer.className = 'p-4 bg-emerald-50 rounded-xl border border-emerald-100 transition-all';
                    statusText.className = 'text-2xl font-black text-emerald-900 tracking-tight';
                }

                const health = Math.max(0, 100 - (maxRms * 80));
                document.getElementById('stat-health').innerText = `${Math.round(health)}%`;
                document.getElementById('stat-health-bar').style.width = `${health}%`;

                document.getElementById('stat-recommendation').innerText = maxRms > 0.45
                    ? "Tingkat getaran meningkat. Disarankan pengecekan fisik pada area mounting mesin."
                    : "Kondisi operasional dalam parameter aman. Optimal.";

                document.getElementById('stat-dominant-freq').innerHTML = `20 <span class="text-xs font-normal text-gray-500">Hz</span>`;
                document.getElementById('stat-peak-amp').innerText = `0.780 g`;
            }

            document.addEventListener('DOMContentLoaded', () => {
                initCharts();
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