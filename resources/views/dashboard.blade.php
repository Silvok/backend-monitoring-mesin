<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl" style="color: #185519;">
                    Dashboard Monitoring Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full border" style="background-color: #f0faf3; border-color: #b3e5c0;">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #2bc970;"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" style="background-color: #118B50;"></span>
                    </div>
                    <span class="text-xs font-semibold" style="color: #185519;">Live</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y, H:i') }}</span>
                </div>
                <button onclick="refreshDashboard()" class="px-4 py-1.5 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm" style="background-color: #118B50;" onmouseover="this.style.backgroundColor='#185519'" onmouseout="this.style.backgroundColor='#118B50'">
                    <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Alert Panel -->
            <div id="alertPanel" class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden border-l-4 border-red-500" style="display: none;">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">
                                Alert Panel - Anomali Terdeteksi
                            </h3>
                            <span id="alertCount" class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-bold">0</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleAlertSound()" id="soundToggle" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                                <svg id="soundIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                </svg>
                                <span id="soundText">On</span>
                            </button>
                            <button onclick="dismissAllAlerts()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                Dismiss All
                            </button>
                            <button onclick="toggleAlertPanel()" class="bg-white/20 hover:bg-white/30 text-white p-1.5 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alert Filters -->
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-semibold text-gray-700">Filter:</span>
                        <button onclick="filterAlerts('all')" class="alert-filter-btn active px-3 py-1 rounded-lg text-sm font-medium transition">
                            All
                        </button>
                        <button onclick="filterAlerts('critical')" class="alert-filter-btn px-3 py-1 rounded-lg text-sm font-medium transition">
                            Critical
                        </button>
                        <button onclick="filterAlerts('high')" class="alert-filter-btn px-3 py-1 rounded-lg text-sm font-medium transition">
                            High
                        </button>
                        <button onclick="filterAlerts('unacknowledged')" class="alert-filter-btn px-3 py-1 rounded-lg text-sm font-medium transition">
                            Unacknowledged
                        </button>
                    </div>
                </div>

                <!-- Alert List -->
                <div id="alertList" class="max-h-96 overflow-y-auto">
                    <!-- Alerts will be dynamically loaded here -->
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Loading alerts...</p>
                    </div>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Mesin Aktif -->
                <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #185519, #118B50);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="rounded-lg p-3" style="background-color: rgba(255,255,255,0.2);">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold" id="totalMachines">{{ $totalMachines }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Total Mesin Aktif</h3>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.8);">Sistem monitoring real-time</p>
                    <div class="mt-2 pt-2 border-t border-white/20">
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">🔄 Update otomatis setiap 5 detik</p>
                    </div>
                </div>

                <!-- Total Data Vibrasi -->
                <div class="rounded-xl shadow-lg p-6 text-white" style="background-color: #187498;">
                    <div class="flex items-center justify-between mb-4">
                        <div class="rounded-lg p-3" style="background-color: rgba(255,255,255,0.2);">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold">{{ number_format($totalSamples) }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Data Vibrasi</h3>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.8);">Total pengukuran (AX, AY, AZ)</p>
                    <div class="mt-2 pt-2 border-t border-white/20">
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">📊 Sampling rate tinggi</p>
                    </div>
                </div>

                <!-- Total Analisis Prediktif -->
                <div class="rounded-xl shadow-lg p-6 text-gray-900" style="background: linear-gradient(to bottom right, #FCCD2A, #F4B942);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="rounded-lg p-3" style="background-color: rgba(255,255,255,0.4);">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold">{{ number_format($totalAnalysis) }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Analisis Prediktif</h3>
                    <p class="text-sm mt-1" style="color: rgba(0,0,0,0.6);">RMS, Peak Amp, Freq Domain</p>
                    <div class="mt-2 pt-2 border-t border-gray-900/20">
                        <p class="text-xs" style="color: rgba(0,0,0,0.5);">🤖 Machine Learning powered</p>
                    </div>
                </div>

                <!-- Status Anomali Vibrasi -->
                <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #B45253, #8B3E3E);">
                    <div class="rounded-lg p-3" style="background-color: rgba(255,255,255,0.2); width: fit-content;">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex items-center justify-between mb-4 mt-4">
                        <h3 class="text-lg font-semibold">Anomali Terdeteksi</h3>
                        <span class="text-3xl font-bold">{{ $anomalyCount }}</span>
                    </div>
                    <p class="text-sm" style="color: rgba(255,255,255,0.8);">Dari {{ $normalCount }} kondisi normal</p>
                    <div class="mt-2 pt-2 border-t border-white/20">
                        <p class="text-xs font-semibold" style="color: rgba(255,255,255,0.9);">Threshold RMS (ISO 10816-3):</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.7);">✅ Normal: 0-2.8 mm/s | ⚠️ Waspada: 2.8-7.1 mm/s | 🚨 Bahaya: >7.1 mm/s</p>
                    </div>
                </div>
            </div>

            <!-- Machine Status Grid - MONITORING SEMUA MESIN -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center" style="color: #185519;">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                    Status Real-Time Semua Mesin
                </h3>
                <p class="text-sm text-gray-600 mb-4">📈 Parameter: RMS Value, Peak Amplitude, Dominant Frequency | ✅ Status: Normal/Anomaly</p>
                <div id="machineStatusGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mx-auto mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2 mx-auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RMS Value Chart - 24 Hours ANALYSIS -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold flex items-center" style="color: #185519;">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Trend Analisis RMS Value (24 Jam)
                        </h3>
                        <p class="text-sm text-gray-600 mt-2">📊 Root Mean Square dari akselerasi vibrasi 3-axis (AX, AY, AZ) | Indikator utama kondisi mesin</p>
                    </div>
                    <div class="flex space-x-3">
                        <div class="bg-blue-50 px-4 py-2 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-800 font-semibold">RMS Formula</p>
                            <p class="text-xs text-blue-600 mt-1">√(AX² + AY² + AZ²)</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-red-50 px-4 py-2 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-800 font-semibold mb-1">Threshold ISO 10816-3 (Class II)</p>
                            <p class="text-xs text-gray-500 mb-2 italic">Velocity-based vibration monitoring</p>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                    <p class="text-xs text-gray-700"><strong>Normal:</strong> 0 - 2.8 mm/s</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                    <p class="text-xs text-gray-700"><strong>Waspada:</strong> 2.8 - 7.1 mm/s</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                    <p class="text-xs text-gray-700"><strong>Bahaya:</strong> > 7.1 mm/s</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="rmsChart"></canvas>
                </div>
            </div>

            <!-- FFT Spectrum Analysis Component -->
            @include('components.fft-spectrum')

            <!-- Top Machines by Risk - PRIORITAS UTAMA -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="mb-4">
                    <h3 class="text-xl font-bold flex items-center" style="color: #185519;">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Prioritas Maintenance Mesin
                    </h3>
                    <p class="text-sm text-gray-600 mt-2">🎯 Ranking berdasarkan tingkat keparahan anomali (Critical → High → Medium → Low)</p>
                </div>
                <div id="topMachinesList" class="space-y-3">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>

            <!-- Latest Sensor Data Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 rounded-t-xl" style="background: linear-gradient(to right, #185519, #118B50);">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Data Sensor Vibrasi Terbaru
                    </h3>
                    <p class="text-white/90 text-sm mt-1">📡 Akselerasi 3-axis (g-force) & Temperatur (°C) - Data mentah dari sensor MPU6050</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mesin</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AX (g)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AY (g)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AZ (g)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Suhu (°C)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($latestSensorData->take(5) as $data)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $data->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" style="color: #185519;">
                                    {{ $data->machine->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($data->ax_g, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($data->ay_g, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($data->az_g, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($data->temperature_c, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data sensor
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        let rmsChart;
        let refreshInterval;
        let isRefreshing = false;

        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
            updateLiveIndicator({{ $anomalyCount }});
            startAutoRefresh();
            startClock();
            initAlertSound();
            loadAlerts();
            loadMachineStatus();
            loadTopMachinesByRisk();
        });

        function initializeChart() {
            const ctx = document.getElementById('rmsChart');
            if (!ctx) {
                console.error('Canvas element tidak ditemukan');
                return;
            }

            const canvasContext = ctx.getContext('2d');
            const rmsData = @json($rmsData);

            console.log('RMS Data:', rmsData);
            console.log('Data Count:', rmsData.length);

            if (!rmsData || rmsData.length === 0) {
                console.warn('Data RMS kosong, menampilkan chart dengan data dummy');
                rmsData.push({ time: 'No Data', value: 0 });
            }

            rmsChart = new Chart(canvasContext, {
                type: 'line',
                data: {
                    labels: rmsData.map(item => item.time),
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: rmsData.map(item => item.value),
                            borderColor: 'rgb(17, 139, 80)',
                            backgroundColor: 'rgba(17, 139, 80, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgb(17, 139, 80)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: 'rgb(24, 85, 25)',
                            pointHoverBorderColor: '#fff'
                        },
                        // Threshold Line - Normal/Waspada boundary (2.8 mm/s) - ISO 10816-3
                        {
                            label: 'Threshold: Normal (2.8 mm/s)',
                            data: Array(rmsData.length).fill(2.8),
                            borderColor: 'rgba(234, 179, 8, 0.7)',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false
                        },
                        // Threshold Line - Waspada/Bahaya boundary (7.1 mm/s) - ISO 10816-3
                        {
                            label: 'Threshold: Bahaya (7.1 mm/s)',
                            data: Array(rmsData.length).fill(7.1),
                            borderColor: 'rgba(239, 68, 68, 0.7),
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: '#185519',
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'RMS: ' + context.parsed.y.toFixed(4);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                color: '#4b5563',
                                callback: function(value) {
                                    return value.toFixed(4);
                                }
                            },
                            title: {
                                display: true,
                                text: 'RMS Value',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#185519'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#4b5563',
                                maxRotation: 45,
                                minRotation: 45
                            },
                            title: {
                                display: true,
                                text: 'Waktu',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#185519'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Auto-refresh dashboard every 5 seconds
        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                refreshDashboard();
            }, 5000); // 5 seconds
        }

        // Manual refresh function
        async function refreshDashboard() {
            if (isRefreshing) {
                console.log('Already refreshing, please wait...');
                return;
            }

            isRefreshing = true;
            const refreshIcon = document.getElementById('refreshIcon');

            console.log('Starting dashboard refresh...');

            // Add spinning animation
            refreshIcon.classList.add('animate-spin');

            try {
                const response = await fetch('/api/dashboard-data');
                console.log('API Response Status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Data received:', data);

                // Update metrics
                const totalMachinesEl = document.getElementById('totalMachines');
                if (totalMachinesEl) {
                    totalMachinesEl.textContent = data.totalMachines;
                }

                // Update chart
                if (rmsChart && data.rmsData) {
                    console.log('Updating chart with', data.rmsData.length, 'data points');
                    rmsChart.data.labels = data.rmsData.map(item => item.time);
                    rmsChart.data.datasets[0].data = data.rmsData.map(item => item.value);
                    rmsChart.update('none'); // Update without animation
                }

                // Update live indicator berdasarkan anomaly count
                updateLiveIndicator(data.anomalyCount);

                console.log('Dashboard refresh completed');

            } catch (error) {
                console.error('Error refreshing dashboard:', error);
                alert('Error refreshing dashboard: ' + error.message);
            } finally {
                refreshIcon.classList.remove('animate-spin');
                isRefreshing = false;
            }
        }

        // Update live indicator color
        function updateLiveIndicator(anomalyCount) {
            try {
                const liveIndicator = document.querySelector('div.flex.items-center.space-x-2.px-3.py-1\\.5.rounded-full');
                if (!liveIndicator) {
                    console.warn('Live indicator element not found');
                    return;
                }

                const dot = liveIndicator.querySelector('div.relative.flex');
                const text = liveIndicator.querySelector('span.text-xs');

                if (!dot || !text) {
                    console.warn('Dot or text element not found');
                    return;
                }

                if (anomalyCount > 0) {
                    // Red alert state
                    liveIndicator.className = 'flex items-center space-x-2 px-3 py-1.5 bg-red-50 rounded-full border border-red-200';
                    dot.innerHTML = `
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    `;
                    text.className = 'text-xs font-semibold text-red-700';
                    text.textContent = 'Alert';
                    console.log('Live indicator updated to Alert state');
                } else {
                    // Green normal state
                    liveIndicator.className = 'flex items-center space-x-2 px-3 py-1.5 rounded-full border';
                    liveIndicator.style.backgroundColor = '#f0faf3';
                    liveIndicator.style.borderColor = '#b3e5c0';
                    dot.innerHTML = `
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #2bc970;"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" style="background-color: #118B50;"></span>
                    `;
                    text.className = 'text-xs font-semibold';
                    text.style.color = '#185519';
                    text.textContent = 'Live';
                    console.log('Live indicator updated to Live state');
                }
            } catch (error) {
                console.error('Error updating live indicator:', error);
            }
        }

        // Update clock every minute - NO SECONDS
        function startClock() {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            function updateClock() {
                const now = new Date();
                const dayName = days[now.getDay()];
                const day = String(now.getDate()).padStart(2, '0');
                const month = months[now.getMonth()];
                const year = now.getFullYear();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                const timeString = `${dayName}, ${day} ${month} ${year}, ${hours}:${minutes}`;
                const timeEl = document.getElementById('currentTime');
                if (timeEl) {
                    timeEl.textContent = timeString;
                    console.log('Clock updated:', timeString);
                }
            }

            updateClock(); // Update immediately
            setInterval(updateClock, 60000); // Update every 60 seconds (1 minute)
        }

        // ============================================
        // MACHINE STATUS GRID
        // ============================================
        async function loadMachineStatus() {
            try {
                const response = await fetch('/api/machine-status');
                const data = await response.json();

                if (data.success && data.machines) {
                    renderMachineStatus(data.machines);
                }
            } catch (error) {
                console.error('Error loading machine status:', error);
            }
        }

        function renderMachineStatus(machines) {
            const grid = document.getElementById('machineStatusGrid');
            if (!grid) return;

            if (machines.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">Tidak ada mesin</div>';
                return;
            }

            grid.innerHTML = machines.map(machine => `
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4" style="border-left-color: ${
                    machine.status === 'ANOMALY' ? '#ef4444' : '#118B50'
                };">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-bold text-gray-900">${machine.name}</h4>
                            <p class="text-xs text-gray-500">${machine.location}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-bold" style="${
                            machine.status === 'ANOMALY'
                                ? 'background-color: #fee2e2; color: #991b1b;'
                                : 'background-color: #e0f5e8; color: #185519;'
                        }">
                            ${machine.status}
                        </span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">RMS:</span>
                            <span class="font-semibold text-gray-900">${machine.rms.toFixed(4)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Peak Amp:</span>
                            <span class="font-semibold text-gray-900">${machine.peak_amp.toFixed(4)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dominant Freq:</span>
                            <span class="font-semibold text-gray-900">${machine.dominant_freq.toFixed(2)} Hz</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-2 pt-2 border-t">
                            ${machine.last_check}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // ============================================
        // TOP MACHINES BY RISK
        // ============================================
        async function loadTopMachinesByRisk() {
            try {
                const response = await fetch('/api/top-machines-by-risk');
                const data = await response.json();

                if (data.success && data.machines) {
                    renderTopMachines(data.machines);
                }
            } catch (error) {
                console.error('Error loading top machines by risk:', error);
            }
        }

        function renderTopMachines(machines) {
            const list = document.getElementById('topMachinesList');
            if (!list) return;

            if (machines.length === 0) {
                list.innerHTML = '<div class="text-center text-gray-500 py-8">Semua mesin dalam kondisi normal</div>';
                return;
            }

            list.innerHTML = machines.map((machine, index) => `
                <div class="bg-gradient-to-r ${
                    machine.severity === 'critical' ? 'from-red-50 to-red-100 border-l-4 border-red-600' :
                    machine.severity === 'high' ? 'from-orange-50 to-orange-100 border-l-4 border-orange-600' :
                    'from-yellow-50 to-yellow-100 border-l-4 border-yellow-600'
                } rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold text-white ${
                                machine.severity === 'critical' ? 'bg-red-600' :
                                machine.severity === 'high' ? 'bg-orange-600' :
                                'bg-yellow-600'
                            }">
                                ${index + 1}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">${machine.machine_name}</h4>
                                <p class="text-sm text-gray-600">${machine.location}</p>
                                <p class="text-xs text-gray-500 mt-1">${machine.time_ago}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold ${
                                machine.severity === 'critical' ? 'text-red-600' :
                                machine.severity === 'high' ? 'text-orange-600' :
                                'text-yellow-600'
                            }">
                                ${machine.rms.toFixed(2)}
                            </p>
                            <span class="text-xs font-semibold uppercase ${
                                machine.severity === 'critical' ? 'text-red-600' :
                                machine.severity === 'high' ? 'text-orange-600' :
                                'text-yellow-600'
                            }">
                                ${machine.severity}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // ============================================
        // ALERT PANEL MANAGEMENT
        // ============================================
        let alertSoundEnabled = true;
        let alertAudio = null;
        let currentAlerts = [];
        let currentFilter = 'all';

        // Initialize alert audio
        function initAlertSound() {
            // Create audio context for alert sound (simple beep)
            alertAudio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZUQ4PVqzn77BnGwc+kunzxG4hBSuBzPLZiTUIGWS67uufURAMT6Tj8LZiFQg2jdfyznkqBSh+x/DdkD8JE1yw6eynVBYKQpzg8b5sIAUugtHy1IAzBhpm/+7EnFMODlKs5++wZxsHO5Dp88RuIQUogcry2Yk0CAMAAEAfAAABAAgA');
        }

        // Load active alerts
        async function loadAlerts() {
            try {
                const response = await fetch('/api/alerts');
                const data = await response.json();

                if (data.success) {
                    currentAlerts = data.alerts;
                    renderAlerts();
                    updateAlertPanel(data.total);
                }
            } catch (error) {
                console.error('Error loading alerts:', error);
            }
        }

        // Render alerts based on current filter
        function renderAlerts() {
            const alertList = document.getElementById('alertList');
            if (!alertList) return;

            let filteredAlerts = currentAlerts;

            // Apply filter
            if (currentFilter === 'critical') {
                filteredAlerts = currentAlerts.filter(a => a.severity === 'critical');
            } else if (currentFilter === 'high') {
                filteredAlerts = currentAlerts.filter(a => a.severity === 'high');
            } else if (currentFilter === 'unacknowledged') {
                filteredAlerts = currentAlerts.filter(a => !a.acknowledged);
            }

            if (filteredAlerts.length === 0) {
                alertList.innerHTML = `
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>No alerts found</p>
                    </div>
                `;
                return;
            }

            alertList.innerHTML = filteredAlerts.map(alert => `
                <div class="alert-item border-b border-gray-200 hover:bg-gray-50 transition ${alert.acknowledged ? 'opacity-50' : ''}" data-alert-id="${alert.id}" data-severity="${alert.severity}">
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <!-- Severity Badge -->
                                    <span class="severity-badge severity-${alert.severity} px-3 py-1 rounded-full text-xs font-bold uppercase">
                                        ${alert.severity}
                                    </span>
                                    <!-- Machine Name -->
                                    <h4 class="font-bold text-gray-900">${alert.machine_name}</h4>
                                    <!-- Location -->
                                    <span class="text-sm text-gray-500">${alert.location}</span>
                                    ${alert.acknowledged ? '<span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-semibold">Acknowledged</span>' : ''}
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-2">
                                    <div>
                                        <p class="text-xs text-gray-500">RMS Value</p>
                                        <p class="font-semibold text-gray-900">${alert.rms.toFixed(4)}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Peak Amplitude</p>
                                        <p class="font-semibold text-gray-900">${alert.peak_amp.toFixed(4)}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Dominant Freq</p>
                                        <p class="font-semibold text-gray-900">${alert.dominant_freq_hz.toFixed(2)} Hz</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ${alert.time_ago}
                                </p>
                            </div>
                            <div class="flex flex-col space-y-2 ml-4">
                                ${!alert.acknowledged ? `
                                    <button onclick="acknowledgeAlert(${alert.id})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                                        Acknowledge
                                    </button>
                                ` : ''}
                                <button onclick="viewAlertDetails(${alert.id})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-medium transition">
                                    Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Update alert panel visibility and count
        function updateAlertPanel(count) {
            const alertPanel = document.getElementById('alertPanel');
            const alertCount = document.getElementById('alertCount');

            if (alertPanel && alertCount) {
                alertCount.textContent = count;
                if (count > 0) {
                    alertPanel.style.display = 'block';
                    if (alertSoundEnabled) {
                        playAlertSound();
                    }
                } else {
                    alertPanel.style.display = 'none';
                }
            }
        }

        // Play alert sound
        function playAlertSound() {
            if (alertAudio) {
                alertAudio.play().catch(e => console.log('Audio play prevented:', e));
            }
        }

        // Toggle alert sound
        function toggleAlertSound() {
            alertSoundEnabled = !alertSoundEnabled;
            const soundIcon = document.getElementById('soundIcon');
            const soundText = document.getElementById('soundText');

            if (alertSoundEnabled) {
                soundText.textContent = 'On';
                soundIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />';
            } else {
                soundText.textContent = 'Off';
                soundIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />';
            }
        }

        // Toggle alert panel visibility
        function toggleAlertPanel() {
            const alertPanel = document.getElementById('alertPanel');
            if (alertPanel) {
                alertPanel.style.display = alertPanel.style.display === 'none' ? 'block' : 'none';
            }
        }

        // Filter alerts
        function filterAlerts(filter) {
            currentFilter = filter;

            // Update filter button styles
            document.querySelectorAll('.alert-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            renderAlerts();
        }

        // Acknowledge alert
        async function acknowledgeAlert(alertId) {
            try {
                const response = await fetch(`/api/alerts/${alertId}/acknowledge`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // Update local state
                    const alert = currentAlerts.find(a => a.id === alertId);
                    if (alert) {
                        alert.acknowledged = true;
                    }
                    renderAlerts();
                }
            } catch (error) {
                console.error('Error acknowledging alert:', error);
            }
        }

        // Dismiss all alerts
        async function dismissAllAlerts() {
            if (!confirm('Are you sure you want to dismiss all alerts?')) return;

            try {
                // Acknowledge all alerts
                for (const alert of currentAlerts) {
                    await acknowledgeAlert(alert.id);
                }

                // Hide panel
                document.getElementById('alertPanel').style.display = 'none';
            } catch (error) {
                console.error('Error dismissing alerts:', error);
            }
        }

        // View alert details
        function viewAlertDetails(alertId) {
            const alert = currentAlerts.find(a => a.id === alertId);
            if (!alert) return;

            alert(`Alert Details:\n\nMachine: ${alert.machine_name}\nLocation: ${alert.location}\nSeverity: ${alert.severity}\n\nRMS: ${alert.rms}\nPeak Amplitude: ${alert.peak_amp}\nDominant Frequency: ${alert.dominant_freq_hz} Hz\n\nTime: ${alert.timestamp}`);
        }

        // ============================================
        // END ALERT PANEL MANAGEMENT
        // ============================================

        // WebSocket Real-time Updates
        function initializeWebSocket() {
            if (typeof window.Echo === 'undefined') {
                console.error('Laravel Echo not initialized');
                return;
            }

            console.log('Initializing WebSocket connection...');

            // Listen to machine status updates
            window.Echo.channel('machines')
                .listen('.machine.status.updated', (e) => {
                    console.log('Machine status update received:', e);

                    // Update metrics
                    if (e.machine_id && e.status) {
                        // Refresh dashboard data to get latest metrics
                        refreshDashboard();

                        // Reload alerts if anomaly detected
                        if (e.status === 'anomaly') {
                            loadAlerts();
                        }

                        // Show notification
                        showNotification(e);
                    }
                });

            console.log('WebSocket listeners registered');
        }

        // Show notification for real-time updates
        function showNotification(data) {
            const notificationHtml = `
                <div class="fixed top-4 right-4 bg-white rounded-lg shadow-xl p-4 border-l-4" style="min-width: 300px; animation: slideIn 0.3s ease-out; border-left-color: ${
                    data.status === 'anomaly' ? '#ef4444' : '#118B50'
                };">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${data.status === 'anomaly' ?
                                '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>' :
                                '<svg class="w-6 h-6" style="color: #118B50;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                            }
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold" style="color: ${data.status === 'anomaly' ? '#991b1b' : '#185519'};">
                                ${data.status === 'anomaly' ? 'Anomali Terdeteksi!' : 'Status Normal'}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>${data.machine_name}</strong> - ${data.location}
                            </p>
                            ${data.prediction ? `<p class="text-xs text-gray-500 mt-1">Prediksi: ${data.prediction}</p>` : ''}
                            <p class="text-xs text-gray-400 mt-1">${data.timestamp}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            const notification = document.createElement('div');
            notification.innerHTML = notificationHtml;
            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });

        // Initialize WebSocket when page loads
        document.addEventListener('DOMContentLoaded', () => {
            initializeWebSocket();
        });
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Alert Panel Styles */
        .alert-filter-btn {
            background: white;
            color: #4B5563;
        }

        .alert-filter-btn.active {
            background: #118B50;
            color: white;
        }

        .severity-badge {
            text-transform: uppercase;
            font-weight: bold;
        }

        .severity-critical {
            background: #FEE2E2;
            color: #991B1B;
        }

        .severity-high {
            background: #FED7AA;
            color: #9A3412;
        }

        .severity-medium {
            background: #FEF3C7;
            color: #92400E;
        }

        .severity-low {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .alert-item:hover {
            background: #F9FAFB;
        }
    </style>
    @endpush
</x-app-layout>
