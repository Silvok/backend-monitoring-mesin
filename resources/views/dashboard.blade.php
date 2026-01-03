<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">
                    Dashboard Monitoring Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Live</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
                <button onclick="refreshDashboard()" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm">
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

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Mesin -->
                <div class="bg-gradient-to-br from-emerald-700 to-emerald-800 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold" id="totalMachines">{{ $totalMachines }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Total Mesin</h3>
                    <p class="text-emerald-100 text-sm mt-1">Mesin terpantau</p>
                </div>

                <!-- Total Samples -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold">{{ number_format($totalSamples) }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Data Sensor</h3>
                    <p class="text-blue-100 text-sm mt-1">Total sample data</p>
                </div>

                <!-- Total Analysis -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold">{{ number_format($totalAnalysis) }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Analisis</h3>
                    <p class="text-yellow-100 text-sm mt-1">Total hasil analisis</p>
                </div>

                <!-- Status Anomali -->
                <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold">{{ $anomalyCount }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">Anomali Terdeteksi</h3>
                    <p class="text-red-100 text-sm mt-1">Dari {{ $normalCount }} kondisi normal</p>
                </div>
            </div>

            <!-- RMS Value Chart - 24 Hours -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-emerald-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Grafik RMS Value (24 Jam Terakhir)
                </h3>
                <div class="relative h-80">
                    <canvas id="rmsChart"></canvas>
                </div>
            </div>

            <!-- Machine Status & Latest Analysis -->
            @if($machine)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Machine Status -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-emerald-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                        Status Mesin
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Mesin</p>
                            <p class="text-lg font-bold text-gray-900">{{ $machine->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tipe</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $machine->type }}</p>
                        </div>
                        @if($machine->latestAnalysis)
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Kondisi Terkini</p>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold
                                @if($machine->latestAnalysis->condition_status === 'NORMAL') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @if($machine->latestAnalysis->condition_status === 'NORMAL')
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                {{ $machine->latestAnalysis->condition_status }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Latest Analysis Details -->
                @if($machine->latestAnalysis)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-emerald-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analisis Terbaru
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-emerald-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">RMS</p>
                            <p class="text-2xl font-bold text-emerald-900">{{ number_format($machine->latestAnalysis->rms, 4) }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Peak Amplitude</p>
                            <p class="text-2xl font-bold text-blue-900">{{ number_format($machine->latestAnalysis->peak_amp, 4) }}</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Dominant Freq</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ number_format($machine->latestAnalysis->dominant_freq_hz, 2) }} Hz</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Waktu Analisis</p>
                            <p class="text-sm font-bold text-purple-900">{{ $machine->latestAnalysis->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Latest Sensor Data Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-700 to-emerald-800 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Data Sensor Terbaru
                    </h3>
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
                            @forelse($latestSensorData as $data)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $data->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-900">
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
                    datasets: [{
                        label: 'RMS Value',
                        data: rmsData.map(item => item.value),
                        borderColor: 'rgb(5, 150, 105)',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(5, 150, 105)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: 'rgb(4, 120, 87)',
                        pointHoverBorderColor: '#fff'
                    }]
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
                                color: '#064e3b',
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
                                color: '#064e3b'
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
                                color: '#064e3b'
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
                    liveIndicator.className = 'flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200';
                    dot.innerHTML = `
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    `;
                    text.className = 'text-xs font-semibold text-emerald-700';
                    text.textContent = 'Live';
                    console.log('Live indicator updated to Live state');
                }
            } catch (error) {
                console.error('Error updating live indicator:', error);
            }
        }

        // Update clock every second
        function startClock() {
            setInterval(() => {
                const now = new Date();
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                document.getElementById('currentTime').textContent = now.toLocaleDateString('id-ID', options);
            }, 1000);
        }

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

                        // Show notification
                        showNotification(e);
                    }
                });

            console.log('WebSocket listeners registered');
        }

        // Show notification for real-time updates
        function showNotification(data) {
            const notificationHtml = `
                <div class="fixed top-4 right-4 bg-white rounded-lg shadow-xl p-4 border-l-4 ${data.status === 'anomaly' ? 'border-red-500' : 'border-emerald-500'} animate-slide-in z-50"
                     style="min-width: 300px; animation: slideIn 0.3s ease-out;">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${data.status === 'anomaly' ?
                                '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>' :
                                '<svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                            }
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold ${data.status === 'anomaly' ? 'text-red-800' : 'text-emerald-800'}">
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
    </style>
    @endpush
</x-app-layout>
