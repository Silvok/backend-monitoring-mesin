<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">Analisis Data Mesin</h2>
                <p class="text-sm text-green-600 font-medium">Monitoring & Insight Mesin</p>
            </div>
            <div class="text-sm text-gray-700 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                <span class="font-semibold" id="currentTime">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
            <!-- Highlight Anomali/Kritis -->
            <div class="bg-gradient-to-r from-red-100 via-orange-100 to-yellow-50 border-l-4 border-red-500 p-6 rounded-2xl shadow-xl" style="display: none;">
                <h3 class="text-lg font-bold text-red-700 mb-2">Mesin Status Anomali/Kritis</h3>
                <ul class="list-disc ml-6">
                    <!-- Loop mesin anomali di sini -->
                    <li class="font-bold text-red-700">Contoh Mesin 1 (Lokasi A) - Status: ANOMALY</li>
                </ul>
            </div>



            <!-- Card Detail Analisis Terbaru -->

        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <!-- Highlight Anomali -->
            @if(isset($anomalyMachines) && count($anomalyMachines) > 0)
            <div class="bg-gradient-to-r from-red-100 via-orange-100 to-yellow-50 border-l-4 border-red-500 p-6 rounded-2xl shadow-xl mb-8">
                <h3 class="text-lg font-bold text-red-700 mb-2">Mesin Status Anomali/Kritis</h3>
                <ul class="list-disc ml-6">
                    @foreach($anomalyMachines as $machine)
                        <li class="font-bold text-red-700">{{ $machine->name }} ({{ $machine->location }}) - Status: {{ $machine->latest_analysis->status ?? '-' }}</li>
                    @endforeach
                </ul>
            </div>
            @endif



            <!-- Health Score Section -->
            <div id="healthScoreSection" class="hidden bg-white rounded-xl shadow-lg border-l-4 border-green-500 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Health Score Analysis</span>
                    </h3>
                </div>
                <div id="healthScoreContent" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Dynamic content akan di-inject di sini -->
                </div>
            </div>

            <!-- Comparative Analysis Section -->
            <div id="comparativeAnalysisSection" class="hidden bg-white rounded-xl shadow-lg border-l-4 border-yellow-500 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Comparative Analysis</span>
                        </h3>
                        <button id="exportCompChartBtn" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-800 font-semibold rounded-lg transition border-2 border-gray-300 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-800 mb-4">Average RMS Value</h4>
                            <div style="height: 300px;">
                                <canvas id="avgRmsChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-800 mb-4">Anomaly Rate Distribution</h4>
                            <div style="height: 300px;">
                                <canvas id="anomalyRateChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistical Summary Section -->
            <div id="statisticalSummarySection" class="hidden bg-white rounded-xl shadow-lg border-l-4 border-green-600 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span>Statistical Summary</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mesin</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Total Data</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">RMS Min</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">RMS Max</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">RMS Avg</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Std Dev</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Anomali</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Rate</th>
                                </tr>
                            <div>
                                <label for="dateTo" class="text-xs font-semibold text-gray-700 mb-1.5 block">
                                    Sampai Tanggal
                                </label>
                                <input type="date" id="dateTo" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition hover:border-green-300 text-sm" value="{{ now()->format('Y-m-d') }}">

                            </div>
                            <div class="flex items-end">
                                <button id="runAnalysisBtn" style="background-color: #16a34a !important; color: white !important; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; gap: 0.4rem; align-items: center; transition: all 0.2s; width: 100%;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold">Jalankan</span>
                                </button>
                            </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button id="exportPdfBtn" class="p-4 bg-red-50 hover:bg-red-100 border-2 border-red-200 rounded-lg transition flex flex-col items-center space-y-2 group">
                            <svg class="w-10 h-10 text-red-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-bold text-gray-900">Export PDF</span>
                            <span class="text-xs text-gray-500">Laporan lengkap</span>
                        </button>
                        <button id="exportExcelBtn" class="p-4 bg-green-50 hover:bg-green-100 border-2 border-green-200 rounded-lg transition flex flex-col items-center space-y-2 group">
                            <svg class="w-10 h-10 text-green-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-bold text-gray-900">Export Excel</span>
                            <span class="text-xs text-gray-500">Format .csv</span>
                        </button>
                        <button id="exportCsvBtn" class="p-4 bg-blue-50 hover:bg-blue-100 border-2 border-blue-200 rounded-lg transition flex flex-col items-center space-y-2 group">
                            <svg class="w-10 h-10 text-blue-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-bold text-gray-900">Export CSV</span>
                            <span class="text-xs text-gray-500">Data mentah</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Anomaly Pattern Analysis Section -->
            <div id="anomalyPatternSection" class="hidden bg-white rounded-xl shadow-lg border-l-4 border-red-500 overflow-hidden">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Anomaly Pattern Analysis</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-800 mb-4">Anomaly Timeline</h4>
                            <div style="height: 300px;">
                                <canvas id="anomalyTimelineChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-800 mb-4">Anomaly Type Distribution</h4>
                            <div style="height: 300px;">
                                <canvas id="anomalyTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Degradation Analysis Section -->
            <div id="degradationSection" class="hidden bg-white rounded-xl shadow-lg border-l-4 border-orange-500 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H3v-2h10v2zm0-4H3v-2h10v2zm0-4H3V7h10v2z"></path>
                        </svg>
                        <span>Degradation Analysis</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-800 mb-4">RMS Trend Over Time</h4>
                            <div style="height: 300px;">
                                <canvas id="rmsTrendChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-800 mb-4">Degradation Rate</h4>
                            <div id="degradationRateContent" class="space-y-4">
                                <!-- Dynamic content akan di-inject di sini -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Correlation Matrix Section -->
            <div id="correlationSection" class="hidden bg-white rounded-xl shadow-lg border-l-4 border-purple-500 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Correlation Matrix</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <div id="correlationMatrixContent" class="min-w-full">
                            <!-- Dynamic heatmap akan di-inject di sini -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="bg-white rounded-xl shadow-lg border border-gray-200 p-12">
                <div class="text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Analisis</h3>
                    <p class="text-gray-600 mb-6">Pilih mesin dan periode waktu, lalu klik "Jalankan Analisis" untuk melihat hasil</p>
                    <button onclick="document.getElementById('runAnalysisBtn').click()" style="background-color: #16a34a !important; color: white !important; padding: 0.625rem 1.5rem; border-radius: 0.5rem; border: none; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.2s;">
                        Mulai Analisis
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        let avgRmsChart = null;
        let anomalyRateChart = null;
        let analysisData = null;

        document.getElementById('runAnalysisBtn').addEventListener('click', function() {
            const machineId = document.getElementById('analysisAllMachines').value;
            const timeRange = document.getElementById('timeRange').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            // Show loading
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('loadingIndicator').classList.remove('hidden');
            hideAllSections();

            // Fetch data
            fetch('/api/analysis', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    machine_id: machineId,
                    time_range: timeRange,
                    date_from: dateFrom,
                    date_to: dateTo
                })
            })
            .then(response => {
                console.log('API Response Status:', response.status);
                return response.json().then(data => {
                    console.log('API Response Data:', data);
                    return { response, data };
                });
            })
            .then(({response, data}) => {
                const analysisData = data;
                document.getElementById('loadingIndicator').classList.add('hidden');

                if (data.health_scores && data.health_scores.length > 0) {
                    renderHealthScores(data.health_scores);
                    renderComparativeAnalysis(data.comparative_data);
                    renderStatisticalSummary(data.statistical_summary);
                    showAllSections();
                } else {
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                console.error('Error details:', error.message);
                document.getElementById('loadingIndicator').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
                console.log('API Error logged. Check console for details.');
            });
        });

        function hideAllSections() {
            const sections = ['healthScoreSection', 'comparativeAnalysisSection', 'statisticalSummarySection',
                            'anomalyPatternSection', 'degradationSection', 'correlationSection'];
            sections.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
        }

        function showAllSections() {
            const sections = ['healthScoreSection', 'comparativeAnalysisSection', 'statisticalSummarySection',
                            'anomalyPatternSection', 'degradationSection', 'correlationSection'];
            sections.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            });
        }

        function renderHealthScores(healthScores) {
            const container = document.getElementById('healthScoreContent');

            container.innerHTML = healthScores.map(machine => {
                const score = machine.health_score;
                let statusClass, statusText, barColor;

                if (score >= 80) {
                    statusClass = 'bg-green-100 text-green-800 border-green-200';
                    statusText = 'Excellent';
                    barColor = 'bg-green-600';
                } else if (score >= 60) {
                    statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    statusText = 'Good';
                    barColor = 'bg-yellow-600';
                } else if (score >= 40) {
                    statusClass = 'bg-orange-100 text-orange-800 border-orange-200';
                    statusText = 'Fair';
                    barColor = 'bg-orange-600';
                } else {
                    statusClass = 'bg-red-100 text-red-800 border-red-200';
                    statusText = 'Critical';
                    barColor = 'bg-red-600';
                }

                return `
                    <div class="bg-gray-50 rounded-lg p-5 border-2 border-gray-200 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-900">${machine.machine_name}</h4>
                            <span class="px-3 py-1 ${statusClass} rounded-full text-xs font-bold border">${statusText}</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Health Score</span>
                                <span class="text-2xl font-bold text-gray-900">${score}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="${barColor} h-3 rounded-full transition-all" style="width: ${score}%"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-white rounded p-2 border border-gray-200">
                                <p class="text-xs text-gray-500 font-semibold">Avg RMS</p>
                                <p class="text-sm font-bold text-gray-900">${machine.avg_rms.toFixed(4)}</p>
                            </div>
                            <div class="bg-white rounded p-2 border border-gray-200">
                                <p class="text-xs text-gray-500 font-semibold">Anomali</p>
                                <p class="text-sm font-bold text-gray-900">${machine.anomaly_count}</p>
                            </div>
                            <div class="bg-white rounded p-2 border border-gray-200">
                                <p class="text-xs text-gray-500 font-semibold">Rate</p>
                                <p class="text-sm font-bold text-gray-900">${machine.anomaly_rate.toFixed(1)}%</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderComparativeAnalysis(comparativeData) {
            // Average RMS Chart
            if (avgRmsChart) avgRmsChart.destroy();

            const ctxAvg = document.getElementById('avgRmsChart').getContext('2d');
            avgRmsChart = new Chart(ctxAvg, {
                type: 'bar',
                data: {
                    labels: comparativeData.machine_names,
                    datasets: [{
                        label: 'Average RMS (G)',
                        data: comparativeData.avg_rms,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#1f2937',
                                font: { size: 12, weight: 'bold' },
                                padding: 15
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Anomaly Rate Chart
            if (anomalyRateChart) anomalyRateChart.destroy();

            const ctxAnomaly = document.getElementById('anomalyRateChart').getContext('2d');
            anomalyRateChart = new Chart(ctxAnomaly, {
                type: 'doughnut',
                data: {
                    labels: comparativeData.machine_names,
                    datasets: [{
                        data: comparativeData.anomaly_rates,
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(84, 187, 67, 0.7)',
                            'rgba(101, 226, 155, 0.7)',
                            'rgba(251, 191, 36, 0.7)',
                            'rgba(250, 204, 21, 0.7)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#1f2937',
                                font: { size: 12, weight: 'bold' },
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        function renderStatisticalSummary(statsSummary) {
            const tbody = document.getElementById('statisticalTableBody');

            tbody.innerHTML = statsSummary.map(stat => `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-900">${stat.machine_name}</td>
                    <td class="px-6 py-4 text-center font-mono text-sm">${stat.total_data}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-green-100 text-green-800 font-mono text-xs font-semibold rounded">${stat.rms_min.toFixed(4)}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-red-100 text-red-800 font-mono text-xs font-semibold rounded">${stat.rms_max.toFixed(4)}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 font-mono text-xs font-semibold rounded">${stat.rms_avg.toFixed(4)}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 font-mono text-xs font-semibold rounded">${stat.std_dev.toFixed(4)}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${stat.anomaly_count > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                            ${stat.anomaly_count}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-orange-100 text-orange-800 font-mono text-xs font-semibold rounded">${stat.anomaly_rate.toFixed(2)}%</span>
                    </td>
                </tr>
            `).join('');
        }

        // Export Handlers
        document.getElementById('exportCompChartBtn').addEventListener('click', function() {
            if (!avgRmsChart) {
                alert('Tidak ada chart untuk di-export');
                return;
            }
            const link = document.createElement('a');
            link.download = 'comparative-analysis.png';
            link.href = avgRmsChart.toBase64Image();
            link.click();
        });

        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            if (!analysisData) {
                alert('Jalankan analisis terlebih dahulu');
                return;
            }
            generatePdfReport();
        });

        document.getElementById('exportExcelBtn').addEventListener('click', exportToExcel);
        document.getElementById('exportCsvBtn').addEventListener('click', exportToExcel);

        function generatePdfReport() {
            const data = analysisData;
            const pdfWindow = window.open('', '_blank');
            pdfWindow.document.write(`
                <html>
                <head>
                    <title>Laporan Analisis Mesin</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 30px; }
                        h1 { color: #16a34a; border-bottom: 3px solid #16a34a; padding-bottom: 10px; }
                        h2 { color: #15803d; margin-top: 30px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                        th { background: #16a34a; color: white; }
                        .score { font-weight: bold; font-size: 18px; }
                        p { color: #333; }
                    </style>
                </head>
                <body>
                    <h1>📊 Laporan Analisis Mesin</h1>
                    <p><strong>Tanggal:</strong> ${new Date().toLocaleDateString('id-ID')}</p>

                    <h2>Health Scores</h2>
                    <table>
                        <tr><th>Mesin</th><th>Score</th><th>Avg RMS</th><th>Anomali</th></tr>
                        ${data.health_scores.map(h => `
                            <tr>
                                <td>${h.machine_name}</td>
                                <td class="score">${h.health_score}/100</td>
                                <td>${h.avg_rms.toFixed(4)}</td>
                                <td>${h.anomaly_count}</td>
                            </tr>
                        `).join('')}
                    </table>

                    <h2>Statistical Summary</h2>
                    <table>
                        <tr>
                            <th>Mesin</th><th>Total Data</th><th>Min</th><th>Max</th>
                            <th>Avg</th><th>Std Dev</th><th>Anomali</th><th>Rate</th>
                        </tr>
                        ${data.statistical_summary.map(s => `
                            <tr>
                                <td>${s.machine_name}</td>
                                <td>${s.total_data}</td>
                                <td>${s.rms_min.toFixed(4)}</td>
                                <td>${s.rms_max.toFixed(4)}</td>
                                <td>${s.rms_avg.toFixed(4)}</td>
                                <td>${s.std_dev.toFixed(4)}</td>
                                <td>${s.anomaly_count}</td>
                                <td>${s.anomaly_rate.toFixed(2)}%</td>
                            </tr>
                        `).join('')}
                    </table>

                    <p style="margin-top: 40px; text-align: center; color: #666; font-size: 12px;">
                        Laporan di-generate otomatis oleh Sistem Monitoring Mesin
                    </p>
                </body>
                </html>
            `);
            pdfWindow.document.close();
            setTimeout(() => pdfWindow.print(), 500);
        }

        function exportToExcel() {
            const data = analysisData.statistical_summary;
            let csv = 'Mesin,Total Data,RMS Min,RMS Max,RMS Avg,Std Dev,Anomali,Rate\n';

            data.forEach(row => {
                csv += `"${row.machine_name}",${row.total_data},${row.rms_min.toFixed(4)},${row.rms_max.toFixed(4)},${row.rms_avg.toFixed(4)},${row.std_dev.toFixed(4)},${row.anomaly_count},${row.anomaly_rate.toFixed(2)}%\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `analisis-mesin-${new Date().getTime()}.csv`;
            link.click();
        }

        // Time range selector handler
        document.getElementById('timeRange').addEventListener('change', function() {
            const days = parseInt(this.value);
            if (days) {
                const today = new Date();
                const pastDate = new Date(today);
                pastDate.setDate(today.getDate() - days);

                document.getElementById('dateTo').value = today.toISOString().split('T')[0];
                document.getElementById('dateFrom').value = pastDate.toISOString().split('T')[0];
            }
        });

        // Render Anomaly Pattern Analysis with dummy data
        let anomalyTimelineChart = null;
        let anomalyTypeChart = null;

        function renderAnomalyPattern() {
            // Anomaly Timeline
            if (anomalyTimelineChart) anomalyTimelineChart.destroy();
            const ctxTimeline = document.getElementById('anomalyTimelineChart').getContext('2d');
            anomalyTimelineChart = new Chart(ctxTimeline, {
                type: 'line',
                data: {
                    labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                    datasets: [{
                        label: 'Anomaly Count',
                        data: [2, 3, 1, 5, 2, 4, 3],
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#1f2937',
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Anomaly Type Distribution
            if (anomalyTypeChart) anomalyTypeChart.destroy();
            const ctxType = document.getElementById('anomalyTypeChart').getContext('2d');
            anomalyTypeChart = new Chart(ctxType, {
                type: 'doughnut',
                data: {
                    labels: ['Vibration Spike', 'Temperature Rise', 'Frequency Shift', 'Pattern Change'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(249, 115, 22, 0.7)',
                            'rgba(251, 191, 36, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#1f2937',
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }

        // Render Degradation Analysis with dummy data
        let rmsTrendChart = null;

        function renderDegradation() {
            // RMS Trend
            if (rmsTrendChart) rmsTrendChart.destroy();
            const ctxTrend = document.getElementById('rmsTrendChart').getContext('2d');
            rmsTrendChart = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
                    datasets: [{
                        label: 'RMS Value (G)',
                        data: [0.45, 0.48, 0.52, 0.56, 0.61, 0.68, 0.75, 0.82],
                        borderColor: 'rgba(249, 115, 22, 1)',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#1f2937',
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1.0
                        }
                    }
                }
            });

            // Degradation Rate
            const degradationRateContent = document.getElementById('degradationRateContent');
            degradationRateContent.innerHTML = `
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-4 border-l-4 border-orange-500">
                        <p class="text-xs text-gray-500 font-semibold">Degradation Rate</p>
                        <p class="text-2xl font-bold text-orange-600">0.045 G/Week</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500">
                        <p class="text-xs text-gray-500 font-semibold">Estimated RUL (Remaining Useful Life)</p>
                        <p class="text-2xl font-bold text-yellow-600">12.5 Weeks</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-red-500">
                        <p class="text-xs text-gray-500 font-semibold">Critical Threshold</p>
                        <p class="text-2xl font-bold text-red-600">1.2 G</p>
                    </div>
                </div>
            `;
        }

        // Render Correlation Matrix with dummy data
        function renderCorrelationMatrix() {
            const correlationMatrixContent = document.getElementById('correlationMatrixContent');

            const correlationData = [
                { x: 'RMS', y: 'RMS', value: 1.00 },
                { x: 'RMS', y: 'Peak Amp', value: 0.92 },
                { x: 'RMS', y: 'Frequency', value: 0.45 },
                { x: 'RMS', y: 'Std Dev', value: 0.87 },

                { x: 'Peak Amp', y: 'RMS', value: 0.92 },
                { x: 'Peak Amp', y: 'Peak Amp', value: 1.00 },
                { x: 'Peak Amp', y: 'Frequency', value: 0.38 },
                { x: 'Peak Amp', y: 'Std Dev', value: 0.79 },

                { x: 'Frequency', y: 'RMS', value: 0.45 },
                { x: 'Frequency', y: 'Peak Amp', value: 0.38 },
                { x: 'Frequency', y: 'Frequency', value: 1.00 },
                { x: 'Frequency', y: 'Std Dev', value: 0.52 },

                { x: 'Std Dev', y: 'RMS', value: 0.87 },
                { x: 'Std Dev', y: 'Peak Amp', value: 0.79 },
                { x: 'Std Dev', y: 'Frequency', value: 0.52 },
                { x: 'Std Dev', y: 'Std Dev', value: 1.00 }
            ];

            const getColor = (value) => {
                if (value >= 0.9) return 'bg-red-600';
                if (value >= 0.7) return 'bg-orange-500';
                if (value >= 0.5) return 'bg-yellow-400';
                if (value >= 0.3) return 'bg-blue-300';
                return 'bg-blue-100';
            };

            let heatmapHTML = '<div class="overflow-x-auto"><table class="border-collapse">';
            heatmapHTML += '<tr><td class="w-20"></td>';
            heatmapHTML += '<td class="text-center font-bold text-sm p-2 border border-gray-300">RMS</td>';
            heatmapHTML += '<td class="text-center font-bold text-sm p-2 border border-gray-300">Peak Amp</td>';
            heatmapHTML += '<td class="text-center font-bold text-sm p-2 border border-gray-300">Frequency</td>';
            heatmapHTML += '<td class="text-center font-bold text-sm p-2 border border-gray-300">Std Dev</td>';
            heatmapHTML += '</tr>';

            const params = ['RMS', 'Peak Amp', 'Frequency', 'Std Dev'];
            params.forEach(param => {
                heatmapHTML += `<tr><td class="font-bold text-sm p-2 border border-gray-300">${param}</td>`;
                params.forEach(param2 => {
                    const data = correlationData.find(d => d.x === param && d.y === param2);
                    const value = data ? data.value : 0;
                    heatmapHTML += `<td class="w-16 h-16 ${getColor(value)} flex items-center justify-center text-white font-bold text-sm border border-gray-300" title="${value.toFixed(2)}">${value.toFixed(2)}</td>`;
                });
                heatmapHTML += '</tr>';
            });

            heatmapHTML += '</table></div>';
            heatmapHTML += '<div class="mt-4 grid grid-cols-5 gap-2"><div class="flex items-center"><div class="w-6 h-6 bg-red-600"></div><span class="text-xs ml-2">0.9 - 1.0</span></div><div class="flex items-center"><div class="w-6 h-6 bg-orange-500"></div><span class="text-xs ml-2">0.7 - 0.9</span></div><div class="flex items-center"><div class="w-6 h-6 bg-yellow-400"></div><span class="text-xs ml-2">0.5 - 0.7</span></div><div class="flex items-center"><div class="w-6 h-6 bg-blue-300"></div><span class="text-xs ml-2">0.3 - 0.5</span></div><div class="flex items-center"><div class="w-6 h-6 bg-blue-100"></div><span class="text-xs ml-2">0.0 - 0.3</span></div></div>';

            correlationMatrixContent.innerHTML = heatmapHTML;
        }

        // Load dummy data on page init
        window.addEventListener('load', function() {
            console.log('Page loaded, showing sections...');

            // Check if elements exist
            const anomalyPatternSection = document.getElementById('anomalyPatternSection');
            const degradationSection = document.getElementById('degradationSection');
            const correlationSection = document.getElementById('correlationSection');

            if (anomalyPatternSection) {
                anomalyPatternSection.classList.remove('hidden');
                console.log('Anomaly Pattern Section shown');
            }
            if (degradationSection) {
                degradationSection.classList.remove('hidden');
                console.log('Degradation Section shown');
            }
            if (correlationSection) {
                correlationSection.classList.remove('hidden');
                console.log('Correlation Section shown');
            }

            // Render with small timeout to ensure DOM is ready
            setTimeout(() => {
                try {
                    renderAnomalyPattern();
                    console.log('Anomaly Pattern rendered');
                } catch (e) {
                    console.error('Error rendering Anomaly Pattern:', e);
                }

                try {
                    renderDegradation();
                    console.log('Degradation rendered');
                } catch (e) {
                    console.error('Error rendering Degradation:', e);
                }

                try {
                    renderCorrelationMatrix();
                    console.log('Correlation Matrix rendered');
                } catch (e) {
                    console.error('Error rendering Correlation Matrix:', e);
                }
            }, 500);
        });
    </script>
</x-app-layout>
