<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script
            src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    @endpush
    <x-slot name="header">
        <!-- ... header code remains same or similar ... -->
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
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Global Filter Card (Simplified for now but functional) -->
            <div class="bg-white shadow-sm border border-emerald-100 overflow-hidden" style="border-radius: 1rem;">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Mesin / Node ESP</label>
                            <select id="filter-machine" onchange="applyFilter()"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Rentang Waktu</label>
                            <select id="filter-time-range" onchange="applyFilter()"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                                <option value="realtime">Real-time (10m)</option>
                                <option value="1h">1 Jam Terakhir</option>
                                <option value="24h">24 Jam Terakhir</option>
                                <option value="7d">7 Hari Terakhir</option>
                                <option value="all" selected>Semua Waktu</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Axis Getaran</label>
                            <select id="filter-axis" onchange="applyFilter()"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                                <option value="x">Sumbu X</option>
                                <option value="y">Sumbu Y</option>
                                <option value="z">Sumbu Z</option>
                                <option value="resultant" selected>Resultant (Total)</option>
                            </select>
                        </div>
                        <div>
                            <button onclick="applyFilter()"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl transition-all shadow-sm">
                                Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div id="module-container" class="space-y-6">
                <!-- Modul Grafik: Time Domain (WAJIB) -->
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col rounded-xl h-[520px]">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analisis Time Domain</h3>
                                <p class="text-[12px] text-gray-500 font-medium">Visualisasi amplitudo getaran (RMS) dan
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
                                    <p class="text-sm font-bold text-gray-600">Pilih mesin untuk menampilkan grafik</p>
                                    <p class="text-[11px] text-gray-400 mt-1">Gunakan wheel mouse untuk zoom, drag untuk
                                        pan</p>
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
            let timeChart;

            function initChart() {
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
            }

            async function applyFilter() {
                const machineId = document.getElementById('filter-machine').value;
                const range = document.getElementById('filter-time-range').value;
                const axis = document.getElementById('filter-axis').value;

                if (!machineId) return;

                document.getElementById('chartPlaceholder').style.opacity = '1';

                try {
                    const response = await fetch(`/api/monitoring/data?machine_id=${machineId}&range=${range}&axis=${axis}`);
                    const data = await response.json();

                    if (data.status === 'success') {
                        document.getElementById('chartPlaceholder').style.opacity = '0';
                        document.getElementById('chartPlaceholder').style.pointerEvents = 'none';

                        timeChart.data.datasets[0].data = data.time_domain.vibration;
                        timeChart.data.datasets[1].data = data.time_domain.temperature;

                        // Update chart scales based on range if needed
                        if (range === 'realtime') {
                            timeChart.options.scales.x.time.unit = 'second';
                        } else if (range === '1h') {
                            timeChart.options.scales.x.time.unit = 'minute';
                        } else if (range === '7d' || range === 'all') {
                            timeChart.options.scales.x.time.unit = 'day';
                        }

                        timeChart.update('none'); // 'none' for instant update without animation glitching during pan
                    }
                } catch (error) {
                    console.error('Error fetching data:', error);
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                initChart();
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