<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">
                    Dashboard Monitoring Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div
                    class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Langsung</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->locale('id')->translatedFormat('d M Y, H:i:s') }}</span>
                </div>
                <button onclick="refreshDashboard()"
                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm">
                    <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Metrics Cards Component -->
            @component('components.dashboard.metrics-cards', compact('totalMachines', 'totalSamples', 'totalAnalysis', 'anomalyCount', 'normalCount'))
            @endcomponent

            <!-- Machine Status Grid Component -->
            @include('components.dashboard.machine-status-grid')

            <!-- RMS Chart Component -->
            @component('components.dashboard.rms-chart', compact('rmsChartData'))
            @endcomponent

            <!-- Alert Panel Component (moved below RMS Chart) -->
            @include('components.dashboard.alert-panel')

            <!-- Top Machines by Risk Component -->
            @include('components.dashboard.top-machines')

            <!-- Latest Sensor Data Table Component -->
            @component('components.dashboard.sensor-data-table', compact('latestSensorData', 'latestTemperatureData'))
            @endcomponent
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <script>
            let refreshInterval;
            let isRefreshing = false;
            let isInitialized = false;

            // Preloaded data from server - NO AJAX needed on initial load!
            const preloadedData = @json($preloadedData ?? []);

            document.addEventListener('DOMContentLoaded', function () {
                if (isInitialized) return;
                isInitialized = true;

                // Use preloaded data immediately - no waiting for AJAX!
                if (preloadedData.machineStatus) {
                    renderMachineStatus(preloadedData.machineStatus);
                }
                if (preloadedData.alerts) {
                    renderAlerts(preloadedData.alerts);
                }
                if (preloadedData.topMachines) {
                    renderTopMachines(preloadedData.topMachines);
                }

                updateLiveIndicator({{ $anomalyCount }});
                startClock();
                initAlertSound();

                // Start auto refresh for subsequent updates
                startAutoRefresh();
                subscribeToRealTimeUpdates();
            });

            function updateLiveIndicator(anomalyCount) {
                const panel = document.getElementById('alertPanel');
                if (panel && anomalyCount > 0) {
                    panel.style.display = 'block';
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
                if (icon) icon.classList.add('animate-spin');

                Promise.all([
                    loadAlerts(),
                    loadMachineStatus(),
                    loadTopMachinesByRisk()
                ]).finally(() => {
                    if (icon) icon.classList.remove('animate-spin');
                    isRefreshing = false;
                });
            }

            function startClock() {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                function updateTime() {
                    const now = new Date();
                    const timeString = `${days[now.getDay()]}, ${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()}, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
                    const el = document.getElementById('currentTime');
                    if (el) el.textContent = timeString;
                }

                updateTime();
                setInterval(updateTime, 60000);
            }

            // Alert Functions - only used for refresh, not initial load
            function loadAlerts() {
                return fetch('/api/alerts')
                    .then(response => response.json())
                    .then(data => {
                        const alerts = Array.isArray(data) ? data : (data.alerts || []);
                        renderAlerts(alerts);
                    })
                    .catch(error => console.error('Error loading alerts:', error));
            }

            function renderAlerts(alerts) {
                const alertList = document.getElementById('alertList');
                const alertCount = document.getElementById('alertCount');
                const alertPanel = document.getElementById('alertPanel');

                alertCount.textContent = alerts.length;

                // Hide panel if no alerts
                if (alerts.length === 0) {
                    if (alertPanel) alertPanel.style.display = 'none';
                    return;
                }

                // Show panel if there are alerts
                if (alertPanel) alertPanel.style.display = 'block';

                alertList.innerHTML = alerts.map(alert => `
                        <div class="px-6 py-4 border-b border-gray-200 hover:bg-red-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">${alert.machine_name}</p>
                                    <p class="text-sm text-gray-600 mt-1">${alert.message}</p>
                                    <p class="text-xs text-gray-400 mt-2">${new Date(alert.created_at).toLocaleString('id-ID')}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-bold ${getSeverityClass(alert.severity)}">
                                    ${alert.severity}
                                </span>
                            </div>
                        </div>
                    `).join('');
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
                    // ISO 10816-3: max scale 11.2 mm/s for visualization
                    const rmsPercent = Math.min((rmsValue / 11.2) * 100, 100);
                    const statusIcon = isNormal
                        ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

                    // Status colors
                    const topBarClass = isNormal ? 'bg-emerald-500' : 'bg-red-500';
                    const statusBgClass = isNormal ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                    const statusText = isNormal ? '✓ NORMAL' : '⚠ ANOMALI';
                    const iconBgClass = isNormal ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600';
                    // ISO 10816-3 Thresholds Class I: < 1.8 (green), 1.8-4.5 (yellow), > 4.5 (red)
                    const progressClass = rmsValue <= 1.8 ? 'bg-emerald-500' : rmsValue <= 4.5 ? 'bg-yellow-500' : 'bg-red-500';

                    return `
                            <div class="group relative bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                                <!-- Status Indicator Bar -->
                                <div class="absolute top-0 left-0 right-0 h-1.5 ${topBarClass}"></div>
                                <div class="absolute top-0 left-0 right-0 h-12 bg-gradient-to-b from-emerald-50 to-transparent opacity-40"></div>

                                <div class="p-5 relative z-10">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-lg text-gray-900 mb-1">${machine.name}</h4>
                                            <p class="text-xs text-gray-500">${machine.location || 'Motor 2 PH 20'}</p>
                                        </div>
                                        <div class="flex items-center justify-center w-10 h-10 rounded-lg ${iconBgClass} flex-shrink-0">
                                            ${statusIcon}
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ${statusBgClass}">
                                            ${statusText}
                                        </span>
                                    </div>

                                    <!-- RMS Value with Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-700">RMS Value</span>
                                            <span class="text-lg font-bold text-gray-900">${rmsValue.toFixed(3)}</span>
                                        </div>
                                        <div class="w-full bg-gray-300 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-full ${progressClass} rounded-full transition-all duration-300" style="width: ${rmsPercent}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Normal: 0 - 1.8 mm/s | Waspada: 1.8 - 4.5 mm/s | Bahaya: > 4.5 mm/s</p>
                                    </div>

                                    <!-- Metrics Grid -->
                                    <div class="grid grid-cols-2 gap-3 mb-4">
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-emerald-50 transition">
                                            <p class="text-xs text-gray-600 font-medium">Peak Amplitude</p>
                                            <p class="text-lg font-bold text-gray-900">${(machine.peak_amp || 0).toFixed(2)}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-emerald-50 transition">
                                            <p class="text-xs text-gray-600 font-medium">Frequency</p>
                                            <p class="text-lg font-bold text-gray-900">${(machine.dominant_freq || 0).toFixed(0)} Hz</p>
                                        </div>
                                    </div>

                                    <!-- Last Check Info -->
                                    <div class="pt-3 border-t border-gray-200">
                                        <p class="text-xs text-gray-600">
                                            <span class="font-semibold text-emerald-600">Last Check:</span> ${machine.last_check || 'No data'}
                                        </p>
                                    </div>
                                </div>
                            </div>
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
                            <div class="text-center text-gray-500 py-8">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Semua mesin dalam kondisi normal</p>
                            </div>
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
                            <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-2xl font-bold text-red-600">#${index + 1}</span>
                                            <div>
                                                <p class="font-semibold text-gray-900">${machineName}</p>
                                                <p class="text-sm text-gray-600">RMS: ${rmsValue.toFixed(4)}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm font-bold ${severityClass}">
                                        ${severityLabel}
                                    </span>
                                </div>
                            </div>
                        `;
                }).join('');
            }

            function renderTopMachinesError(message) {
                const list = document.getElementById('topMachinesList');
                list.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m0-10a4 4 0 110 8 4 4 0 010-8z" />
                            </svg>
                            <p>${message}</p>
                        </div>
                    `;
            }

            // Alert Sound Control
            let alertSoundEnabled = true;
            function initAlertSound() {
                const soundToggle = document.getElementById('soundToggle');
                if (soundToggle) {
                    soundToggle.addEventListener('click', toggleAlertSound);
                }
            }

            function toggleAlertSound() {
                alertSoundEnabled = !alertSoundEnabled;
                const soundText = document.getElementById('soundText');
                const soundIcon = document.getElementById('soundIcon');

                if (soundText) soundText.textContent = alertSoundEnabled ? 'On' : 'Off';
                if (soundIcon) soundIcon.classList.toggle('opacity-50');
            }

            // WebSocket Subscription
            function subscribeToRealTimeUpdates() {
                if (window.Echo) {
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
                if (event && event.target) {
                    event.target.classList.add('active');
                }
                // Logic filtering would go here if needed, but for now just visual toggle
            }
        </script>
    @endpush
</x-app-layout>
