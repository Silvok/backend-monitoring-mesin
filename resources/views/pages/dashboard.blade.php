<x-app-layout>
    <x-slot name="header">
        <div class="w-full min-w-0 flex items-center justify-between gap-2">
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-base sm:text-xl text-emerald-900 truncate">
                    {{ __('messages.app.dashboard_title') }}
                </h2>
            </div>
            <div class="flex-shrink-0">
                <div class="inline-flex items-center text-[10px] sm:text-sm text-gray-600 bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold whitespace-nowrap tabular-nums" id="currentTime">{{ now()->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
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
            const AUTO_REFRESH_INTERVAL_MS = 10000;

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
                refreshDashboard({ force: true });
                startAutoRefresh();
                subscribeToRealTimeUpdates();

                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        refreshDashboard({ force: true });
                    }
                });

                window.addEventListener('monitoring:auto-refresh', () => {
                    refreshDashboard({ force: false });
                });
            });

            function updateLiveIndicator(anomalyCount) {
                const panel = document.getElementById('alertPanel');
                if (panel && anomalyCount > 0) {
                    panel.style.display = 'block';
                }
            }

            function startAutoRefresh() {
                refreshInterval = setInterval(() => {
                    refreshDashboard({ force: false });
                }, AUTO_REFRESH_INTERVAL_MS);
            }

            function refreshDashboard({ force = false } = {}) {
                if (isRefreshing) {
                    return;
                }

                if (!force && document.hidden) {
                    return;
                }

                isRefreshing = true;
                const icon = document.getElementById('refreshIcon');
                if (icon) icon.classList.add('animate-spin');

                Promise.all([
                    loadDashboardSummary(),
                    loadAlerts(),
                    loadMachineStatus(),
                    loadTopMachinesByRisk()
                ]).finally(() => {
                    if (icon) icon.classList.remove('animate-spin');
                    isRefreshing = false;
                });
            }

            function startClock() {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                function updateTime() {
                    const now = new Date();
                    const timeString = `${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()}, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
                    const el = document.getElementById('currentTime');
                    if (el) el.textContent = timeString;
                }

                updateTime();
                setInterval(updateTime, 60000);
            }

            function loadDashboardSummary() {
                return fetch('/api/dashboard-data')
                    .then(async response => {
                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        renderMetricsCards(data);
                        updateLiveIndicator(Number(data.anomalyCount || 0));
                        if (window.updateDashboardRmsChart && data.rmsChartData) {
                            window.updateDashboardRmsChart(data.rmsChartData);
                        }
                        if (Array.isArray(data.latestSensorData)) {
                            renderLatestSensorTable(data.latestSensorData);
                        }
                    })
                    .catch(error => console.error('Error loading dashboard summary:', error));
            }

            function renderMetricsCards(data) {
                const totalMachinesEl = document.getElementById('totalMachines');
                const totalSamplesEl = document.getElementById('totalSamples');
                const totalAnalysisEl = document.getElementById('totalAnalysis');
                const anomalyCountEl = document.getElementById('anomalyCount');
                const normalCountTextEl = document.getElementById('normalCountText');

                if (totalMachinesEl) totalMachinesEl.textContent = formatInteger(data.totalMachines || 0);
                if (totalSamplesEl) totalSamplesEl.textContent = formatInteger(data.totalSamples || 0);
                if (totalAnalysisEl) totalAnalysisEl.textContent = formatInteger(data.totalAnalysis || 0);
                if (anomalyCountEl) anomalyCountEl.textContent = formatInteger(data.anomalyCount || 0);
                if (normalCountTextEl) {
                    const template = normalCountTextEl.dataset.template || '__COUNT__';
                    normalCountTextEl.textContent = template.replace('__COUNT__', formatInteger(data.normalCount || 0));
                }
            }

            function renderLatestSensorTable(rows) {
                const body = document.getElementById('latestSensorTableBody');
                if (!body) {
                    return;
                }

                if (!rows.length) {
                    body.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                {{ __('messages.dashboard.no_sensor_data') }}
                            </td>
                        </tr>
                    `;
                    return;
                }

                body.innerHTML = rows.map(row => {
                    const temperature = row.temperature_c;
                    const temperatureClass = temperature === null
                        ? 'text-gray-500'
                        : (temperature > 50 ? 'text-red-600' : (temperature > 40 ? 'text-orange-600' : 'text-green-600'));

                    return `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">${formatDateTime(row.timestamp)}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-semibold text-emerald-900">${escapeHtml(row.machine_name || 'N/A')}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">${formatDecimal(row.ax_g, 4)}</td>
                            <td class="hidden sm:table-cell px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">${formatDecimal(row.ay_g, 4)}</td>
                            <td class="hidden md:table-cell px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">${formatDecimal(row.az_g, 4)}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-semibold ${temperatureClass}">${temperature === null ? 'N/A' : `${formatDecimal(temperature, 2)}°C`}</td>
                        </tr>
                    `;
                }).join('');
            }

            function formatInteger(value) {
                return Number(value || 0).toLocaleString('id-ID');
            }

            function formatDecimal(value, digits) {
                return Number(value || 0).toFixed(digits);
            }

            function formatDateTime(value) {
                if (!value) return '-';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return value;
                return date.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function escapeHtml(value) {
                const div = document.createElement('div');
                div.textContent = String(value ?? '');
                return div.innerHTML;
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

                if (!alertList || !alertCount) {
                    return;
                }

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
                                    <p class="font-semibold text-gray-900">${escapeHtml(alert.machine_name || 'Unknown')}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        RMS ${formatDecimal(alert.rms, 3)} mm/s${alert.location ? ` - ${escapeHtml(alert.location)}` : ''}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">${formatDateTime(alert.timestamp || alert.created_at)}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-bold ${getSeverityClass(alert.severity || '')}">
                                    ${String(alert.severity || 'unknown').toUpperCase()}
                                </span>
                            </div>
                        </div>
                    `).join('');
            }

            function getSeverityClass(severity) {
                const classes = {
                    critical: 'bg-red-100 text-red-800',
                    high: 'bg-orange-100 text-orange-800',
                    medium: 'bg-yellow-100 text-yellow-800',
                    low: 'bg-blue-100 text-blue-800'
                };
                return classes[String(severity).toLowerCase()] || 'bg-gray-100 text-gray-800';
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
                    grid.innerHTML = `<div class="col-span-3 text-center text-gray-500">{{ __('messages.dashboard.no_machine_data') }}</div>`;
                    return;
                }

                grid.innerHTML = machines.map((machine, index) => {
                    const isNormal = machine.status === 'NORMAL';
                    const isActive = machine.is_active !== false;
                    const rmsValue = machine.rms || 0;
                    // Max scale for visualization
                    const rmsPercent = Math.min((rmsValue / 11.2) * 100, 100);
                    const statusIcon = isNormal
                        ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

                    // Status colors
                    const topBarClass = !isActive ? 'bg-gray-400' : (isNormal ? 'bg-emerald-500' : 'bg-red-500');
                    const statusBgClass = isNormal ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                    const statusText = isNormal ? '✓ NORMAL' : '⚠ ANOMALI';
                    const iconBgClass = isNormal ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600';
                    // Thresholds: < 25.0 (green), 25.0-28.0 (yellow), > 28.0 (red)
                    const progressClass = rmsValue <= 25.0 ? 'bg-emerald-500' : rmsValue <= 28.0 ? 'bg-yellow-500' : 'bg-red-500';
                    const activeBadge = isActive
                        ? '<span class="ml-2 px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">ON</span>'
                        : '<span class="ml-2 px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">OFF</span>';

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
                                    <div class="mb-4 flex items-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ${statusBgClass}">
                                            ${statusText}
                                        </span>
                                        ${activeBadge}
                                    </div>

                                    <!-- RMS Value with Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-700">{{ __('messages.dashboard.rms_value') }}</span>
                                            <span class="text-lg font-bold text-gray-900">${rmsValue.toFixed(3)}</span>
                                        </div>
                                        <div class="w-full bg-gray-300 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-full ${progressClass} rounded-full transition-all duration-300" style="width: ${rmsPercent}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.dashboard.normal_range') }} | {{ __('messages.dashboard.warning_range') }} | {{ __('messages.dashboard.danger_range') }}</p>
                                    </div>

                                    <!-- Metrics Grid -->
                                    <div class="grid grid-cols-2 gap-3 mb-4">
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-emerald-50 transition">
                                            <p class="text-xs text-gray-600 font-medium">{{ __('messages.dashboard.peak_amplitude') }}</p>
                                            <p class="text-lg font-bold text-gray-900">${(machine.peak_amp || 0).toFixed(2)}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-emerald-50 transition">
                                            <p class="text-xs text-gray-600 font-medium">{{ __('messages.dashboard.frequency') }}</p>
                                            <p class="text-lg font-bold text-gray-900">${(machine.dominant_freq || 0).toFixed(0)} Hz</p>
                                        </div>
                                    </div>

                                    <!-- Last Check Info -->
                                    <div class="pt-3 border-t border-gray-200">
                                        <p class="text-xs text-gray-600">
                                            <span class="font-semibold text-emerald-600">{{ __('messages.dashboard.last_check') }}:</span> ${machine.last_check || '{{ __('messages.dashboard.no_data') }}'}
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
                                <p>{{ __('messages.dashboard.all_normal') }}</p>
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
                if (!window.Echo) {
                    return;
                }

                const machinesChannel = window.Echo.channel('machines');

                machinesChannel.listen('.machine.status.updated', () => {
                    refreshDashboard({ force: true });
                });

                machinesChannel.listen('.analysis.updated', () => {
                    refreshDashboard({ force: true });
                    if (alertSoundEnabled) {
                        playAlertSound();
                    }
                });

                machinesChannel.listen('.sensor.updated', () => {
                    loadDashboardSummary();
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
                if (event && event.target) {
                    event.target.classList.add('active');
                }
                // Logic filtering would go here if needed, but for now just visual toggle
            }
        </script>
    @endpush
</x-app-layout>



