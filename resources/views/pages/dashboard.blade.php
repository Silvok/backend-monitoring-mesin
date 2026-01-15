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
                    <span class="font-semibold" id="currentTime">{{ now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
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
            <!-- Alert Panel Component -->
            @include('components.dashboard.alert-panel')

            <!-- Metrics Cards Component -->
            @component('components.dashboard.metrics-cards', compact('totalMachines', 'totalSamples', 'totalAnalysis', 'anomalyCount', 'normalCount'))
            @endcomponent

            <!-- Machine Status Grid Component -->
            @include('components.dashboard.machine-status-grid')

            <!-- RMS Chart Component -->
            @component('components.dashboard.rms-chart', compact('rmsChartData'))
            @endcomponent

            <!-- Top Machines by Risk Component -->
            @include('components.dashboard.top-machines')

            <!-- Latest Sensor Data Table Component -->
            @component('components.dashboard.sensor-data-table', compact('latestSensorData', 'latestTemperatureData'))
            @endcomponent
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        let rmsChart;
        let refreshInterval;
        let isRefreshing = false;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired');

            // Ensure all elements are ready before initializing
            setTimeout(() => {
                initializeChart();
                updateLiveIndicator({{ $anomalyCount }});
                startAutoRefresh();
                startClock();
                initAlertSound();
                loadAlerts();
                loadMachineStatus();
                loadTopMachinesByRisk();
                subscribeToRealTimeUpdates();
            }, 100);
        });

        // Also load data immediately as fallback
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                loadMachineStatus();
                loadAlerts();
                loadTopMachinesByRisk();
            });
        } else {
            // DOM is already loaded
            loadMachineStatus();
            loadAlerts();
            loadTopMachinesByRisk();
        }

        function initializeChart() {
            const ctx = document.getElementById('rmsChart').getContext('2d');

            const chartData = @json($rmsChartData);

            rmsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: chartData.values,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'RMS Value (mm/s)'
                            }
                        }
                    }
                }
            });
        }

        function updateLiveIndicator(anomalyCount) {
            if (anomalyCount > 0) {
                document.getElementById('alertPanel').style.display = 'block';
            }
        }

        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                if (!isRefreshing) {
                    refreshDashboard();
                }
            }, 30000);
        }

        function refreshDashboard() {
            isRefreshing = true;
            const icon = document.getElementById('refreshIcon');
            icon.classList.add('animate-spin');

            Promise.all([
                loadAlerts(),
                loadMachineStatus(),
                loadTopMachinesByRisk()
            ]).finally(() => {
                icon.classList.remove('animate-spin');
                isRefreshing = false;
            });
        }

        function startClock() {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            function updateTime() {
                const now = new Date();
                const dayName = days[now.getDay()];
                const day = String(now.getDate()).padStart(2, '0');
                const month = months[now.getMonth()];
                const year = now.getFullYear();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                const timeString = `${dayName}, ${day} ${month} ${year}, ${hours}:${minutes}`;
                document.getElementById('currentTime').textContent = timeString;
            }

            updateTime(); // Update immediately
            setInterval(updateTime, 60000); // Update every minute
        }

        // Alert Functions
        function loadAlerts() {
            return fetch('/api/alerts')
                .then(response => response.json())
                .then(data => {
                    console.log('Alerts data:', data);
                    // Handle both array response and wrapped response
                    const alerts = Array.isArray(data) ? data : (data.alerts || []);
                    renderAlerts(alerts);
                })
                .catch(error => console.error('Error loading alerts:', error));
        }

        function renderAlerts(alerts) {
            const alertList = document.getElementById('alertList');
            const alertCount = document.getElementById('alertCount');

            alertCount.textContent = alerts.length;

            if (alerts.length === 0) {
                alertList.innerHTML = `
                    <div     class="px-6 py-8 text-center text-gray-500">
                        <svg     class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <pat    h stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </sv    g>
                        <p>T    idak ada alert</p>
                    </di    v>
                `;    
                return;
            }

            alertList.innerHTML = alerts.map(alert => `
                <div     class="px-6 py-4 border-b border-gray-200 hover:bg-red-50 transition">
                    <div     class="flex items-start justify-between">
                        <div     class="flex-1">
                            <p c    lass="font-semibold text-gray-900">${alert.machine_name}</p>
                            <p c    lass="text-sm text-gray-600 mt-1">${alert.message}</p>
                            <p c    lass="text-xs text-gray-400 mt-2">${new Date(alert.created_at).toLocaleString('id-ID')}</p>
                        </di    v>
                        <spa    n class="px-3 py-1 rounded-full text-sm font-bold ${getSeverityClass(alert.severity)}">
                            ${al    ert.severity}
                        </sp    an>
                    </di    v>
                </di    v>
            `).j    oin('');
        }

        function getSeverityClass(severity) {
            const classes = {
                'CRITICAL': 'bg-red-100 text-red-800',
                'HIGH': 'bg-orange-100 text-orange-800',
                'MEDIUM': 'bg-yellow-100 text-yellow-800',
                'LOW': 'bg-blue-100 text-blue-800'
            };
            return classes[severity] || 'bg-gray-100 text-gray-800';
        }

        // Machine Status Functions
        function loadMachineStatus() {
            const grid = document.getElementById('machineStatusGrid');

            // If grid doesn't exist, retry after a short delay
            if (!grid) {
                console.warn('machineStatusGrid not found, retrying...');
                setTimeout(loadMachineStatus, 100);
                return;
            }

            return fetch('/api/machine-status')
                .then(response => response.json())
                .then(data => {
                    console.log('Machine status data:', data);
                    renderMachineStatus(data.machines || data);
                })
                .catch(error => console.error('Error loading machine status:', error));
        }

        function renderMachineStatus(machines) {
            const grid = document.getElementById('machineStatusGrid');

            if (!grid) {
                console.error('Grid element not found');
                return;
            }

            if (!machines || machines.length === 0) {
                grid.innerHTML = '<div class="col-span-3 text-center text-gray-500">Tidak ada data mesin</div>';
                return;
            }

            grid.innerHTML = machines.map((machine, index) => {
                const isNormal = machine.status === 'NORMAL';
                const rmsValue = machine.rms || 0;
                const rmsPercent = Math.min((rmsValue / 3) * 100, 100);
                const statusIcon = isNormal
                    ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                    : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

                // Status colors
                const topBarClass = isNormal ? 'bg-emerald-500' : 'bg-red-500';
                const statusBgClass = isNormal ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                const statusText = isNormal ? '✓ NORMAL' : '⚠ ANOMALI';
                const iconBgClass = isNormal ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600';
                const progressClass = rmsValue <= 0.5 ? 'bg-emerald-500' : rmsValue <= 1.5 ? 'bg-yellow-500' : 'bg-red-500';

                return `
                    <div     class="group relative bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <!--     Status Indicator Bar with Green Accent -->
                        <div     class="absolute top-0 left-0 right-0 h-1.5 ${topBarClass}"></div>
                        <div     class="absolute top-0 left-0 right-0 h-12 bg-gradient-to-b from-emerald-50 to-transparent opacity-40"></div>

                        <div     class="p-5 relative z-10">
                            <!--     Header -->
                            <div     class="flex items-start justify-between mb-4">
                                <div     class="flex-1">
                                    <h4     class="font-bold text-lg text-gray-900 mb-1">${machine.name}</h4>
                                    <p c    lass="text-xs text-gray-500">${machine.location || 'Location N/A'}</p>
                                </di    v>
                                <div     class="flex items-center justify-center w-10 h-10 rounded-lg ${iconBgClass} flex-shrink-0">
                                    ${st    atusIcon}
                                </di    v>
                            </di    v>

                            <!--     Status Badge -->
                            <div     class="mb-4">
                                <spa    n class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ${statusBgClass}">
                                    ${st    atusText}
                                </sp    an>
                            </di    v>

                            <!--     RMS Value with Progress Bar -->
                            <div     class="mb-4">
                                <div     class="flex items-center justify-between mb-2">
                                    <spa    n class="text-sm font-semibold text-gray-700">RMS Value</span>
                                    <spa    n class="text-lg font-bold text-gray-900">${rmsValue.toFixed(3)}</span>
                                </di    v>
                                <div     class="w-full bg-gray-300 rounded-full h-2.5 overflow-hidden">
                                    <div     class="h-full ${progressClass} rounded-full transition-all duration-300" style="width: ${rmsPercent}%"></div>
                                </di    v>
                                <p c    lass="text-xs text-gray-500 mt-1">Normal: 0 - 0.7g | Waspada: 0.7 - 1.8g | Bahaya: > 1.8g</p>
                            </di    v>

                            <!--     Metrics Grid -->
                            <div     class="grid grid-cols-2 gap-3 mb-4">
                                <div     class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-emerald-50 transition">
                                    <p c    lass="text-xs text-gray-600 font-medium">Peak Amplitude</p>
                                    <p c    lass="text-lg font-bold text-gray-900">${(machine.peak_amp || 0).toFixed(2)}</p>
                                </di    v>
                                <div     class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-emerald-50 transition">
                                    <p c    lass="text-xs text-gray-600 font-medium">Frequency</p>
                                    <p c    lass="text-lg font-bold text-gray-900">${(machine.dominant_freq || 0).toFixed(0)} Hz</p>
                                </di    v>
                            </di    v>

                            <!--     Last Check Info -->
                            <div     class="pt-3 border-t border-gray-200">
                                <p c    lass="text-xs text-gray-600">
                                    <spa    n class="font-semibold text-emerald-600">Last Check:</span> ${machine.last_check || 'No data'}
                                </p>    
                            </di    v>
                        </di    v>
                    </di    v>
                `;    
            }).join('');
        }

        // Top Machines Functions
        function loadTopMachinesByRisk() {
            return fetch('/api/top-machines-by-risk')
                .then(async response => {
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(`HTTP ${response.status}: ${text}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Top machines data:', data);
                    const machines = Array.isArray(data)
                        ? data
                        : (data.machines || data.top_machines || data.data || []);
                    renderTopMachines(machines);
                })
                .catch(error => {
                    console.error('Error loading top machines:', error);
                    renderTopMachinesError('Gagal memuat ranking mesin. Coba refresh atau cek koneksi.');
                });
        }

        function renderTopMachines(machines) {
            const list = document.getElementById('topMachinesList');

            const rows = Array.isArray(machines) ? machines : [];

            if (!rows.length) {
                list.innerHTML = `
                    <div     class="text-center text-gray-500 py-8">
                        <svg     class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <pat    h stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </sv    g>
                        <p>S    emua mesin dalam kondisi normal</p>
                    </di    v>
                `;    
                return;
            }

            list.innerHTML = rows.map((machine, index) => {
                const machineName = machine.machine_name || machine.name || 'Nama tidak tersedia';
                const rmsValue = Number(machine.rms || 0);
                const severityClass = rmsValue > 10
                    ? 'bg-red-100 text-red-800'
                    : rmsValue > 5
                        ? 'bg-orange-100 text-orange-800'
                        : 'bg-yellow-100 text-yellow-800';
                const severityLabel = rmsValue > 10 ? 'CRITICAL' : rmsValue > 5 ? 'HIGH' : 'MEDIUM';

                return `
                <div     class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-500">
                    <div     class="flex items-center justify-between">
                        <div     class="flex-1">
                            <div     class="flex items-center space-x-2">
                                <spa    n class="text-2xl font-bold text-red-600">#${index + 1}</span>
                                <div    >
                                    <p c    lass="font-semibold text-gray-900">${machineName}</p>
                                    <p c    lass="text-sm text-gray-600">RMS: ${rmsValue.toFixed(4)}</p>
                                </di    v>
                            </di    v>
                        </di    v>
                        <spa    n class="px-3 py-1 rounded-full text-sm font-bold ${severityClass}">
                            ${se    verityLabel}
                        </sp    an>
                    </di    v>
                </di    v>
                `;    
            }).join('');
        }

        function renderTopMachinesError(message) {
            const list = document.getElementById('topMachinesList');
            list.innerHTML = `
                <div     class="text-center text-gray-500 py-8">
                    <svg     class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <pat    h stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m0-10a4 4 0 110 8 4 4 0 010-8z" />
                    </sv    g>
                    <p>$    {message}</p>
                </di    v>
            `;    
        }

        // Alert Sound Control
        let alertSoundEnabled = true;
        function initAlertSound() {
            const soundToggle = document.getElementById('soundToggle');
            soundToggle.addEventListener('click', toggleAlertSound);
        }

        function toggleAlertSound() {
            alertSoundEnabled = !alertSoundEnabled;
            const soundText = document.getElementById('soundText');
            const soundIcon = document.getElementById('soundIcon');

            soundText.textContent = alertSoundEnabled ? 'On' : 'Off';
            soundIcon.classList.toggle('opacity-50');
        }

        // WebSocket Subscription
        function subscribeToRealTimeUpdates() {
            window.Echo.channel('machines').listen('MachineStatusUpdated', (event) => {
                console.log('Machine status updated:', event);
                loadMachineStatus();
                loadTopMachinesByRisk();
            });

            window.Echo.channel('machines').listen('AnomalyDetected', (event) => {
                console.log('Anomaly detected:', event);
                loadAlerts();
                if (alertSoundEnabled) {
                    playAlertSound();
                }
            });
        }

        function playAlertSound() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        }

        // Alert Panel Controls
        function toggleAlertPanel() {
            const panel = document.getElementById('alertPanel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }

        function dismissAllAlerts() {
            fetch('/api/alerts/machine/all/dismiss', { method: 'POST' })
                .then(() => loadAlerts())
                .catch(error => console.error('Error dismissing alerts:', error));
        }

        function filterAlerts(type) {
            const buttons = document.querySelectorAll('.alert-filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
    @endpush
</x-app-layout>
