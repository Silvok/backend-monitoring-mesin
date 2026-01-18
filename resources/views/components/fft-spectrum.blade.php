<div id="fft-spectrum-card" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-xl font-bold flex items-center" style="color: #185519;">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                FFT Spectrum Analysis
            </h3>
            <p id="fft-machine-name" class="text-sm text-gray-600 mt-2">📊 Fast Fourier Transform - Analisis frekuensi dominan untuk deteksi ketidakseimbangan & kerusakan bearing</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Machine Selector -->
            <select id="fft-machine-select" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">Semua Mesin</option>
            </select>
            <!-- Refresh Button -->
            <button id="fft-refresh-btn" class="px-4 py-1.5 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm" style="background-color: #118B50;" onmouseover="this.style.backgroundColor='#185519'" onmouseout="this.style.backgroundColor='#118B50'" title="Refresh FFT Data">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- FFT Chart -->
    <div class="relative h-64 mb-4">
        <canvas id="fft-spectrum-chart"></canvas>
        <div id="fft-loading" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-800/80 hidden">
            <div class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-600 dark:text-gray-400">Loading FFT data...</span>
            </div>
        </div>
        <div id="fft-no-data" class="absolute inset-0 flex items-center justify-center hidden">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="mt-2 text-gray-500 dark:text-gray-400">No FFT data available</p>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <!-- Dominant Frequency -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Frekuensi Dominan</p>
            <p id="fft-dominant-freq" class="text-lg font-bold" style="color: #118B50;">- Hz</p>
        </div>
        <!-- Max Amplitude -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Amplitudo Max</p>
            <p id="fft-max-amplitude" class="text-lg font-bold text-blue-600 dark:text-blue-400">- g</p>
        </div>
        <!-- Frequency Range -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rentang Frekuensi</p>
            <p id="fft-freq-range" class="text-lg font-bold text-purple-600 dark:text-purple-400">-</p>
        </div>
        <!-- Last Updated -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Terakhir Update</p>
            <p id="fft-timestamp" class="text-sm font-bold text-gray-600 dark:text-gray-300">-</p>
        </div>
    </div>

    <!-- Dominant Frequencies Table -->
    <div class="mb-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Top 5 Frekuensi Dominan</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Frekuensi (Hz)</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amplitudo (g)</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Level</th>
                    </tr>
                </thead>
                <tbody id="fft-dominant-table" class="divide-y divide-gray-200 dark:divide-gray-600">
                    <tr>
                        <td colspan="4" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">Data tidak tersedia</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Band Analysis -->
    <div>
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Analisis Band Frekuensi</h4>
        <div id="fft-band-analysis" class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
                <span class="text-xs text-gray-500 dark:text-gray-400">No band data</span>
            </div>
        </div>
    </div>
</div>

<script>
class FFTSpectrumComponent {
    constructor() {
        this.chart = null;
        this.currentMachineId = '';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadMachines();
        this.loadFFTSpectrum();
    }

    setupEventListeners() {
        const machineSelect = document.getElementById('fft-machine-select');
        const refreshBtn = document.getElementById('fft-refresh-btn');

        if (machineSelect) {
            machineSelect.addEventListener('change', (e) => {
                this.currentMachineId = e.target.value;
                this.loadFFTSpectrum();
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.loadFFTSpectrum();
            });
        }
    }

    async loadMachines() {
        try {
            const response = await fetch('/api/machine-status');
            const data = await response.json();

            if (data.success && data.data) {
                const select = document.getElementById('fft-machine-select');
                const machines = data.data;

                machines.forEach(machine => {
                    const option = document.createElement('option');
                    option.value = machine.id;
                    option.textContent = machine.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading machines:', error);
        }
    }

    async loadFFTSpectrum() {
        this.showLoading(true);
        this.hideNoData();

        try {
            let url = '/api/fft/spectrum';
            if (this.currentMachineId) {
                url += `?machine_id=${this.currentMachineId}`;
            }

            const response = await fetch(url);
            const result = await response.json();

            if (result.success && result.data) {
                this.renderChart(result.data);
                this.updateStatistics(result.data);
                this.updateDominantTable(result.data);
                this.updateBandAnalysis(result.data);
            } else {
                this.showNoData();
            }
        } catch (error) {
            console.error('Error loading FFT spectrum:', error);
            this.showNoData();
        } finally {
            this.showLoading(false);
        }
    }

    renderChart(data) {
        const ctx = document.getElementById('fft-spectrum-chart');
        if (!ctx) return;

        if (this.chart) {
            this.chart.destroy();
        }

        const chartData = data.chart_data;

        this.chart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
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
                            title: (items) => `Frekuensi: ${items[0].label} Hz`,
                            label: (item) => `Amplitudo: ${item.formattedValue} g`
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Frekuensi (Hz)',
                            color: '#185519',
                            font: { size: 11, weight: 'bold' }
                        },
                        ticks: {
                            maxTicksLimit: 10,
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Amplitudo (g)',
                            color: '#185519',
                            font: { size: 11, weight: 'bold' }
                        },
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.2)'
                        }
                    }
                }
            }
        });
    }

    updateStatistics(data) {
        const stats = data.statistics;

        document.getElementById('fft-machine-name').textContent = data.machine_name;
        document.getElementById('fft-dominant-freq').textContent = `${stats.dominant_frequency} Hz`;
        document.getElementById('fft-max-amplitude').textContent = `${stats.max_amplitude.toFixed(4)} g`;
        document.getElementById('fft-freq-range').textContent = `${stats.frequency_range.min}-${stats.frequency_range.max} Hz`;
        document.getElementById('fft-timestamp').textContent = data.timestamp;
    }

    updateDominantTable(data) {
        const tbody = document.getElementById('fft-dominant-table');
        if (!tbody) return;

        // Extract dominant frequencies from harmonics or calculate from chart data
        const chartData = data.chart_data;
        const frequencies = chartData.labels;
        const amplitudes = chartData.datasets[0].data;

        // Combine and sort by amplitude
        const combined = frequencies.map((f, i) => ({
            frequency: f,
            amplitude: amplitudes[i]
        })).sort((a, b) => b.amplitude - a.amplitude).slice(0, 5);

        if (combined.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">No data available</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = combined.map((item, index) => {
            const level = this.getAmplitudeLevel(item.amplitude);
            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">${index + 1}</td>
                    <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200">${item.frequency} Hz</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">${item.amplitude.toFixed(6)} g</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${level.class}">${level.label}</span>
                    </td>
                </tr>
            `;
        }).join('');
    }

    getAmplitudeLevel(amplitude) {
        if (amplitude > 0.1) {
            return { label: 'High', class: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' };
        } else if (amplitude > 0.01) {
            return { label: 'Medium', class: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' };
        } else {
            return { label: 'Low', class: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' };
        }
    }

    updateBandAnalysis(data) {
        const container = document.getElementById('fft-band-analysis');
        if (!container || !data.band_analysis) return;

        const bandColors = {
            'sub_synchronous': 'from-blue-400 to-blue-600',
            'low_freq': 'from-green-400 to-green-600',
            'mid_freq': 'from-yellow-400 to-yellow-600',
            'high_freq': 'from-red-400 to-red-600'
        };

        container.innerHTML = data.band_analysis.map(band => `
            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-gradient-to-r ${bandColors[band.band] || 'from-gray-400 to-gray-600'}"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 truncate">${band.label.split('(')[0].trim()}</span>
                </div>
                <p class="text-sm font-bold text-gray-800 dark:text-white">${band.energy.toFixed(4)}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${band.points} points</p>
            </div>
        `).join('');
    }

    showLoading(show) {
        const loading = document.getElementById('fft-loading');
        if (loading) {
            loading.classList.toggle('hidden', !show);
        }
    }

    showNoData() {
        const noData = document.getElementById('fft-no-data');
        if (noData) {
            noData.classList.remove('hidden');
        }
    }

    hideNoData() {
        const noData = document.getElementById('fft-no-data');
        if (noData) {
            noData.classList.add('hidden');
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('fft-spectrum-card')) {
        window.fftComponent = new FFTSpectrumComponent();
    }
});
</script>
