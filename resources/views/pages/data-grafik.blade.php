<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">Data Grafik</h2>
                <p class="text-sm text-green-600 font-medium">Visualisasi Data RMS Value</p>
            </div>
            <div class="text-sm text-gray-700 bg-gradient-to-br from-green-50 to-emerald-50 px-4 py-2.5 rounded-lg border-2 border-green-200 shadow-sm">
                <span class="font-bold" id="currentDate">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Panel -->
            <div class="bg-gradient-to-br from-white via-green-50/30 to-emerald-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Machine Selector -->
                    <div>
                        <label for="graphMachineSelector" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                            </svg>
                            <span>Pilih Mesin</span>
                        </label>
                        <select id="graphMachineSelector" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 font-medium bg-white shadow-sm transition hover:border-green-300">
                            <option value="">-- Pilih Mesin --</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->location }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="dateFrom" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Dari Tanggal</span>
                        </label>
                        <input type="date" id="dateFrom" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition hover:border-green-300" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="dateTo" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Sampai Tanggal</span>
                        </label>
                        <input type="date" id="dateTo" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition hover:border-green-300" value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <!-- Quick Presets & Actions -->
                    <div>
                        <label class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Jangka Waktu</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="timePreset" class="flex-1 px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 font-medium bg-white shadow-sm transition hover:border-green-300">
                                <option value="">Pilih Preset</option>
                                <option value="7">Terakhir 7 Hari</option>
                                <option value="14">Terakhir 14 Hari</option>
                                <option value="30">Terakhir 30 Hari</option>
                                <option value="60">Terakhir 60 Hari</option>
                                <option value="90">Terakhir 90 Hari</option>
                            </select>
                            <button id="applyFilterBtn" class="p-2.5 bg-green-100 text-green-700 font-bold rounded-lg hover:bg-green-200 hover:text-green-800 active:bg-green-300 transition-all duration-200 shadow-md hover:shadow-lg border-2 border-green-300 hover:border-green-400" title="Terapkan Filter">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </button>
                            <button id="exportCsvBtn" class="p-2.5 bg-green-100 text-green-700 font-bold rounded-lg hover:bg-green-200 hover:text-green-800 active:bg-green-300 transition-all duration-200 shadow-md hover:shadow-lg border-2 border-green-300 hover:border-green-400" title="Download CSV">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden p-5 bg-gradient-to-r from-green-50 via-emerald-50 to-green-50 border-l-4 border-green-500 rounded-lg flex items-center space-x-3 shadow-lg">
                <div class="animate-spin">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <span class="text-sm text-green-800 font-bold">Memuat data grafik...</span>
            </div>

            <!-- Trend Chart Section -->
            <div id="trendChartSection" class="bg-gradient-to-br from-white to-green-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6 hidden">
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-green-100">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Grafik Trend RMS Value</h3>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-2 rounded-lg border-2 border-green-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span id="chartDateRange" class="font-semibold">-</span>
                    </div>
                </div>
                <div class="relative bg-gray-50 rounded-lg p-4" style="height: 450px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Statistics Summary Section -->
            <div id="statsSection" class="bg-gradient-to-br from-white to-green-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6 hidden">
                <div class="flex items-center space-x-3 mb-5 pb-4 border-b-2 border-green-100">
                    <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan Statistik</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold text-gray-800">Metrik</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Min</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Max</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Rata-rata</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Total Data</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Anomali</th>
                            </tr>
                        </thead>
                        <tbody id="statsTableBody">
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Silakan pilih mesin dan filter untuk melihat data statistik
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-white rounded-full shadow-md mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Data Grafik</h3>
                    <p class="text-gray-500 max-w-md">Pilih mesin dan atur rentang tanggal untuk menampilkan grafik trend RMS Value dan statistik data</p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.js"></script>
    <script>
        let trendChart = null;
        let graphData = {
            labels: [],
            rmsValues: [],
            rawData: []
        };

        // Update current date in header
        function updateCurrentDate() {
            const now = new Date();
            document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Time Preset Handler
        document.getElementById('timePreset').addEventListener('change', function() {
            const days = parseInt(this.value);
            if (days > 0) {
                const toDate = new Date();
                const fromDate = new Date();
                fromDate.setDate(toDate.getDate() - days);

                document.getElementById('dateTo').valueAsDate = toDate;
                document.getElementById('dateFrom').valueAsDate = fromDate;
            }
        });

        // Apply Filter Button
        document.getElementById('applyFilterBtn').addEventListener('click', function() {
            loadGraphData();
        });

        // Load Graph Data
        function loadGraphData() {
            const machineId = document.getElementById('graphMachineSelector').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            if (!machineId) {
                alert('Silakan pilih mesin terlebih dahulu');
                return;
            }

            if (!dateFrom || !dateTo) {
                alert('Silakan pilih tanggal dari dan sampai');
                return;
            }

            // Show loading
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('trendChartSection').classList.add('hidden');
            document.getElementById('statsSection').classList.add('hidden');

            // Fetch data from API
            fetch(`/api/machine/${machineId}/historical-trend?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingIndicator').classList.add('hidden');

                    if (data.success && data.data && data.data.length > 0) {
                        document.getElementById('emptyState').classList.add('hidden');
                        processGraphData(data.data, data.machine_name, dateFrom, dateTo);
                        renderChart();
                        renderStatistics(data);
                        document.getElementById('trendChartSection').classList.remove('hidden');
                        document.getElementById('statsSection').classList.remove('hidden');
                    } else {
                        document.getElementById('emptyState').classList.remove('hidden');
                        document.getElementById('trendChartSection').classList.add('hidden');
                        document.getElementById('statsSection').classList.add('hidden');
                        console.log('Tidak ada data untuk tanggal yang dipilih');
                    }
                })
                .catch(error => {
                    console.error('Error loading graph data:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                });
        }

        // Process Graph Data
        function processGraphData(data, machineName, dateFrom, dateTo) {
            graphData.labels = [];
            graphData.rmsValues = [];
            graphData.rawData = data;

            // Group by date if too many points
            if (data.length > 100) {
                // Aggregate by day
                const aggregated = {};
                data.forEach(item => {
                    const date = item.timestamp.split(' ')[0];
                    if (!aggregated[date]) {
                        aggregated[date] = [];
                    }
                    aggregated[date].push(parseFloat(item.rms_value || 0));
                });

                Object.keys(aggregated).sort().forEach(date => {
                    const values = aggregated[date];
                    const avg = values.reduce((a, b) => a + b, 0) / values.length;
                    graphData.labels.push(date);
                    graphData.rmsValues.push(avg.toFixed(4));
                });
            } else {
                // Show each point
                data.forEach(item => {
                    const timestamp = item.timestamp.split(' ')[1] || item.timestamp;
                    graphData.labels.push(timestamp);
                    graphData.rmsValues.push(parseFloat(item.rms_value || 0).toFixed(4));
                });
            }

            // Update chart date range
            document.getElementById('chartDateRange').textContent = `${dateFrom} hingga ${dateTo}`;
        }

        // Render Chart
        function renderChart() {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;

            // Destroy existing chart
            if (trendChart) {
                trendChart.destroy();
            }

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: graphData.labels,
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: graphData.rmsValues,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: graphData.labels.length > 50 ? 0 : 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                padding: 15,
                                font: { size: 13, weight: 'bold' },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                afterLabel: function(context) {
                                    return 'G-Force';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'RMS Value (G)'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        // Render Statistics
        function renderStatistics(data) {
            const tbody = document.getElementById('statsTableBody');

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data</td></tr>';
                return;
            }

            const rmsValues = data.data.map(item => parseFloat(item.rms_value || 0));
            const anomalyCount = data.data.filter(item => item.is_anomaly === 1).length;

            const min = Math.min(...rmsValues).toFixed(4);
            const max = Math.max(...rmsValues).toFixed(4);
            const avg = (rmsValues.reduce((a, b) => a + b, 0) / rmsValues.length).toFixed(4);

            tbody.innerHTML = `
                <tr class="border-b border-gray-200 hover:bg-blue-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-900">RMS Value</td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-green-100 text-green-800 font-mono text-sm font-semibold rounded-lg">${min} G</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-red-100 text-red-800 font-mono text-sm font-semibold rounded-lg">${max} G</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 font-mono text-sm font-semibold rounded-lg">${avg} G</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 font-mono text-sm font-semibold rounded-lg">${rmsValues.length}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-4 py-1.5 rounded-full text-sm font-bold shadow-sm ${anomalyCount > 0 ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'}">
                            ${anomalyCount}
                        </span>
                    </td>
                </tr>
            `;
        }

        // Export to CSV
        document.getElementById('exportCsvBtn').addEventListener('click', function() {
            if (graphData.rawData.length === 0) {
                alert('Tidak ada data untuk di-export');
                return;
            }

            const machineId = document.getElementById('graphMachineSelector').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            let csv = 'Timestamp,RMS Value (G),Peak Amplitude,Dominant Frequency,Status\n';

            graphData.rawData.forEach(item => {
                const row = [
                    item.timestamp,
                    item.rms_value || '0',
                    item.peak_amplitude || '0',
                    item.dominant_frequency || '0',
                    item.is_anomaly === 1 ? 'ANOMALI' : 'NORMAL'
                ];
                csv += row.map(val => `"${val}"`).join(',') + '\n';
            });

            // Download
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            const fileName = `grafik-rms-${machineId}-${dateFrom}-${dateTo}.csv`;

            link.setAttribute('href', url);
            link.setAttribute('download', fileName);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentDate();
        });
    </script>
</x-app-layout>
