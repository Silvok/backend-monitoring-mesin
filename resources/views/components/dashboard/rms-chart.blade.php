<!-- RMS Value Chart -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4 sm:mb-6">
        <h3 class="text-lg sm:text-xl font-bold text-emerald-900 flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            {{ __('messages.dashboard.rms_trend') }}
        </h3>

        <div class="w-full lg:w-auto lg:ml-auto flex flex-wrap justify-start lg:justify-end items-center gap-2 lg:gap-3">
            <div class="flex items-center bg-gray-100 rounded-full shadow-sm p-1 gap-1">
                <button id="rmsLiveModeBtn"
                    class="px-4 py-2 text-xs font-semibold rounded-full bg-emerald-500 text-white shadow-md transition transform hover:scale-105">
                    Langsung
                </button>
                <button id="rmsHistoricalModeBtn"
                    class="px-4 py-2 text-xs font-semibold rounded-full bg-white text-gray-900 border border-gray-300 hover:border-emerald-400 hover:text-emerald-600 transition">
                    Historis
                </button>
            </div>

            <div id="rmsHistoricalControls" class="hidden flex-wrap items-center gap-2 xl:gap-3">
                <input id="rmsDateFrom" type="date"
                    class="px-4 py-2 rounded-full border border-slate-200 bg-white shadow-sm text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-300">
                <input id="rmsDateTo" type="date"
                    class="px-4 py-2 rounded-full border border-slate-200 bg-white shadow-sm text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-300">
                <button id="rmsApplyRangeBtn" type="button"
                    class="px-4 py-2 rounded-full bg-emerald-500 text-white text-xs font-semibold shadow-md hover:bg-emerald-600 transition">
                    Terapkan
                </button>
                <button id="rmsResetRangeBtn" type="button"
                    class="px-4 py-2 rounded-full border border-slate-200 bg-white text-slate-700 text-xs font-semibold shadow-sm hover:bg-gray-50 transition">
                    24 Jam
                </button>
            </div>

            <div id="rmsLiveIndicator"
                class="flex items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-sky-50 to-emerald-50 rounded-full border border-sky-200 shadow-sm">
                <div class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </div>
                <span class="text-xs font-semibold text-sky-700">Pembaruan Langsung</span>
            </div>

            <div id="rmsHistoricalIndicator"
                class="hidden flex items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-purple-50 to-pink-50 rounded-full border border-purple-200 shadow-sm">
                <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-xs font-semibold text-purple-700">Data Historis</span>
            </div>

            <div id="rmsHistoricalLoading"
                class="hidden items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-full border border-blue-200 shadow-sm">
                <svg class="animate-spin w-3 h-3 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-semibold text-blue-700">Memuat data...</span>
            </div>

            <span id="rmsRangeInfo" class="text-xs text-gray-500"></span>
        </div>
    </div>
    <div class="flex items-center justify-between mb-3">
        <label class="flex items-center gap-2 text-xs text-gray-600">
            <input id="rmsScaleToggle" type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" checked>
            {{ __('messages.dashboard.auto_zoom') }}
        </label>
        <span class="text-[11px] text-gray-600">{{ __('messages.dashboard.full_scale') }}</span>
    </div>
    <div class="relative h-64 sm:h-80">
        <canvas id="rmsChart" data-chart="{{ json_encode($rmsChartData ?? []) }}"></canvas>
    </div>
    <div class="flex mt-4 justify-end">
        <button id="resetZoomBtn" type="button"
            class="mr-2 px-3 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg border border-gray-300 shadow text-xs font-semibold">
            Reset Zoom
        </button>
        <div class="relative">
            <button id="downloadBtn" type="button"
                class="p-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow text-xs" aria-label="{{ __('messages.dashboard.download') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
            </button>
            <div id="downloadMenu"
                class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10 hidden">
                <button type="button"
                    class="block w-full text-left px-4 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                    data-format="png">PNG</button>
                <button type="button"
                    class="block w-full text-left px-4 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                    data-format="jpg">JPG</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', async function () {
        let chartData = normalizeChartData(JSON.parse(document.getElementById('rmsChart').dataset.chart));
        const ctx = document.getElementById('rmsChart').getContext('2d');
        let chartType = 'line';
        const chartTypeSelect = document.getElementById('chartType');
        const scaleToggle = document.getElementById('rmsScaleToggle');
        const chartContainer = document.getElementById('rmsChart');
        let chartInstance = null;
        let useAutoScale = true;

        function normalizeChartData(data) {
            return {
                labels: Array.isArray(data?.labels) ? data.labels : [],
                values: Array.isArray(data?.values) ? data.values : [],
                full_times: Array.isArray(data?.full_times) ? data.full_times : [],
                machines: Array.isArray(data?.machines) ? data.machines : [],
                statuses: Array.isArray(data?.statuses) ? data.statuses : [],
            };
        }

        function parseToEpochMs(timeString) {
            if (!timeString) return null;
            // Backend sends "YYYY-MM-DD HH:mm:ss". Convert to ISO-like local time for Date parsing.
            const normalized = String(timeString).replace(' ', 'T');
            const parsed = new Date(normalized);
            const time = parsed.getTime();
            return Number.isFinite(time) ? time : null;
        }

        function buildTimeSeriesPoints() {
            const points = [];
            const values = chartData.values || [];
            const fullTimes = chartData.full_times || [];
            const labels = chartData.labels || [];

            for (let i = 0; i < values.length; i++) {
                const sourceTime = fullTimes[i] || labels[i];
                const epochMs = parseToEpochMs(sourceTime);
                if (epochMs === null) continue;

                points.push({
                    x: epochMs,
                    y: values[i],
                    idx: i
                });
            }

            return points;
        }

        function renderChart(type) {
            if (chartInstance) chartInstance.destroy();
            // Highlight anomaly/critical/fault points
            const statusArr = chartData.statuses || [];
            const highlightStatuses = ['ANOMALY', 'FAULT', 'CRITICAL', 'WARNING'];
            const values = chartData.values || [];
            const points = buildTimeSeriesPoints();
            let xMin = points.length ? points[0].x : undefined;
            let xMax = points.length ? points[points.length - 1].x : undefined;
            if (xMin !== undefined && xMax !== undefined && xMin === xMax) {
                // Keep scale renderable when only one point is available.
                xMax = xMin + 60000;
            }
            let yMin = 0;
            let yMax = 0;
            if (values.length) {
                const minVal = Math.min(...values);
                const sorted = [...values].sort((a, b) => a - b);
                const p95Index = Math.max(0, Math.floor(0.95 * (sorted.length - 1)));
                const p95Val = sorted[p95Index];
                const autoMax = Math.max(p95Val, minVal);
                const pad = Math.max(0.05, (autoMax - minVal) * 0.2);
                yMin = Math.max(0, minVal - pad);
                yMax = autoMax + pad;
            }
            // Untuk bar: array warna background, untuk line: array warna point
            const barColors = values.map((_, i) => {
                if (highlightStatuses.includes((statusArr[i] || '').toUpperCase())) {
                    return 'rgba(239,68,68,0.7)'; // merah
                }
                return 'rgba(5,150,105,0.3)'; // hijau
            });
            const pointColors = points.map((point) => {
                const i = point.idx;
                if (highlightStatuses.includes((statusArr[i] || '').toUpperCase())) {
                    return '#ef4444'; // merah
                }
                return '#059669'; // hijau
            });
            chartInstance = new Chart(ctx, {
                type: type,
                data: {
                    labels: type === 'bar' ? (chartData.labels || []) : undefined,
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: type === 'bar' ? values : points,
                            borderColor: '#059669',
                            backgroundColor: type === 'bar' ? barColors : 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 3,
                            fill: type === 'line',
                            tension: type === 'line' ? 0.2 : undefined,
                            pointBackgroundColor: type === 'line' ? pointColors : undefined,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
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
                        },
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'x'
                            },
                            zoom: {
                                wheel: { enabled: true },
                                pinch: { enabled: true },
                                mode: 'x'
                            },
                            limits: {
                                x: { minRange: 60 * 1000 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function (context) {
                                    const idx = context[0].raw?.idx ?? context[0].dataIndex;
                                    let waktu = chartData.full_times && chartData.full_times[idx] ? chartData.full_times[idx] : context[0].label;
                                    return '{{ __('messages.dashboard.time') }}: ' + waktu;
                                },
                                label: function (context) {
                                    const idx = context.raw?.idx ?? context.dataIndex;
                                    let rms = context.parsed.y;
                                    let label = '{{ __('messages.dashboard.rms_label') }}: ' + rms + ' mm/s';
                                    if (chartData.machines && chartData.machines[idx]) {
                                        label += ' | {{ __('messages.dashboard.machine') }}: ' + chartData.machines[idx];
                                    }
                                    if (chartData.statuses && chartData.statuses[idx]) {
                                        label += ' | {{ __('messages.dashboard.status') }}: ' + chartData.statuses[idx];
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: useAutoScale ? undefined : 0,
                            max: useAutoScale ? undefined : 11.2,
                            suggestedMin: useAutoScale ? yMin : undefined,
                            suggestedMax: useAutoScale ? yMax : undefined,
                            grid: {
                                color: '#d1d5db'
                            },
                            ticks: {
                                color: '#374151',
                                font: { size: 12, weight: '600' }
                            },
                            title: {
                                display: true,
                                text: 'RMS Value (mm/s)'
                            }
                        },
                        x: {
                            type: type === 'bar' ? 'category' : 'linear',
                            bounds: type === 'bar' ? 'ticks' : 'data',
                            min: type === 'bar' ? undefined : xMin,
                            max: type === 'bar' ? undefined : xMax,
                            offset: false,
                            grid: {
                                color: '#e5e7eb'
                            },
                            ticks: {
                                color: '#6b7280',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10,
                                callback: function (value) {
                                    if (type === 'bar') return this.getLabelForValue(value);
                                    const date = new Date(Number(value));
                                    if (Number.isNaN(date.getTime())) return '';
                                    return date.toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: false
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }

        async function ensureChartReady() {
            if (window.ensureChartJs) {
                try {
                    await window.ensureChartJs();
                } catch (error) {
                    console.error('Failed to load Chart.js:', error);
                    return false;
                }
            }
            return true;
        }

        async function ensureZoomPluginReady() {
            if (window.__rmsZoomPluginReady) {
                return true;
            }

            const tryRegister = () => {
                const zoomPlugin = window.ChartZoom || window['chartjs-plugin-zoom'] || window.zoomPlugin;
                if (window.Chart && zoomPlugin && !window.__rmsZoomPluginRegistered) {
                    window.Chart.register(zoomPlugin);
                    window.__rmsZoomPluginRegistered = true;
                }
                if (window.Chart && window.__rmsZoomPluginRegistered) {
                    window.__rmsZoomPluginReady = true;
                    return true;
                }
                return false;
            };

            if (tryRegister()) {
                return true;
            }

            if (window.__rmsZoomPluginLoading) {
                return new Promise((resolve) => {
                    const check = () => {
                        if (window.__rmsZoomPluginReady) return resolve(true);
                        if (window.__rmsZoomPluginFailed) return resolve(false);
                        setTimeout(check, 100);
                    };
                    check();
                });
            }

            window.__rmsZoomPluginLoading = true;
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js';
            script.async = true;

            const loaded = await new Promise((resolve) => {
                script.onload = () => resolve(true);
                script.onerror = () => resolve(false);
                document.head.appendChild(script);
            });

            if (!loaded) {
                window.__rmsZoomPluginFailed = true;
                return false;
            }

            return tryRegister();
        }

        let initialized = false;
        async function initWhenVisible() {
            if (initialized) return;
            initialized = true;
            const ready = await ensureChartReady();
            if (!ready) return;
            await ensureZoomPluginReady();
            renderChart(chartType);
        }

        if ('IntersectionObserver' in window && chartContainer) {
            const observer = new IntersectionObserver((entries, io) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    io.disconnect();
                    initWhenVisible();
                }
            }, { rootMargin: '200px' });

            observer.observe(chartContainer);
        } else {
            initWhenVisible();
        }

        if (scaleToggle) {
            scaleToggle.addEventListener('change', function () {
                useAutoScale = scaleToggle.checked;
                if (chartInstance) renderChart(chartType);
            });
        }

        if (chartTypeSelect) {
            chartTypeSelect.addEventListener('change', function (e) {
                chartType = e.target.value;
                if (chartInstance) renderChart(chartType);
            });
        }

        // Download button with popup menu
        const downloadBtn = document.getElementById('downloadBtn');
        const downloadMenu = document.getElementById('downloadMenu');
        const dateFromInput = document.getElementById('rmsDateFrom');
        const dateToInput = document.getElementById('rmsDateTo');
        const applyRangeBtn = document.getElementById('rmsApplyRangeBtn');
        const resetRangeBtn = document.getElementById('rmsResetRangeBtn');
        const rangeInfo = document.getElementById('rmsRangeInfo');
        const liveModeBtn = document.getElementById('rmsLiveModeBtn');
        const historicalModeBtn = document.getElementById('rmsHistoricalModeBtn');
        const historicalControls = document.getElementById('rmsHistoricalControls');
        const liveIndicator = document.getElementById('rmsLiveIndicator');
        const historicalIndicator = document.getElementById('rmsHistoricalIndicator');
        const historicalLoading = document.getElementById('rmsHistoricalLoading');
        const resetZoomBtn = document.getElementById('resetZoomBtn');
        let historyMode = false;

        function setRangeInfo(message, isError = false) {
            if (!rangeInfo) return;
            rangeInfo.textContent = message || '';
            rangeInfo.classList.remove('text-gray-500', 'text-red-600');
            rangeInfo.classList.add(isError ? 'text-red-600' : 'text-gray-500');
        }

        function getTodayDateString() {
            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function setDefaultDateInputs() {
            const today = getTodayDateString();
            if (dateFromInput && !dateFromInput.value) dateFromInput.value = today;
            if (dateToInput && !dateToInput.value) dateToInput.value = today;
        }

        function setMode(mode, { refresh = true } = {}) {
            const isHistorical = mode === 'historical';
            historyMode = isHistorical;

            if (liveModeBtn) {
                liveModeBtn.className = isHistorical
                    ? 'px-4 py-2 text-xs font-semibold rounded-full bg-white text-gray-900 border border-gray-300 hover:border-emerald-400 hover:text-emerald-600 transition'
                    : 'px-4 py-2 text-xs font-semibold rounded-full bg-emerald-500 text-white shadow-md transition transform hover:scale-105';
            }
            if (historicalModeBtn) {
                historicalModeBtn.className = isHistorical
                    ? 'px-4 py-2 text-xs font-semibold rounded-full bg-emerald-500 text-white shadow-md transition transform hover:scale-105'
                    : 'px-4 py-2 text-xs font-semibold rounded-full bg-white text-gray-900 border border-gray-300 hover:border-emerald-400 hover:text-emerald-600 transition';
            }

            if (historicalControls) {
                historicalControls.classList.toggle('hidden', !isHistorical);
                historicalControls.classList.toggle('flex', isHistorical);
            }
            if (liveIndicator) {
                liveIndicator.classList.toggle('hidden', isHistorical);
                liveIndicator.classList.toggle('flex', !isHistorical);
            }
            if (historicalIndicator) {
                historicalIndicator.classList.toggle('hidden', !isHistorical);
                historicalIndicator.classList.toggle('flex', isHistorical);
            }

            if (isHistorical) {
                setRangeInfo('Pilih rentang lalu klik Terapkan.');
            } else {
                setRangeInfo('Mode live 24 jam terakhir.');
                if (refresh && typeof window.forceDashboardSummaryRefresh === 'function') {
                    window.forceDashboardSummaryRefresh();
                }
            }
        }

        async function fetchRmsHistory(dateFrom, dateTo) {
            const params = new URLSearchParams({
                date_from: dateFrom,
                date_to: dateTo,
            });
            const response = await fetch(`/api/dashboard-rms-trend?${params.toString()}`);
            const payload = await response.json();
            if (!response.ok || !payload.success) {
                throw new Error(payload.message || `HTTP ${response.status}`);
            }
            return payload;
        }

        async function applyHistoryRange() {
            const dateFrom = dateFromInput?.value || '';
            const dateTo = dateToInput?.value || '';

            if (!dateFrom || !dateTo) {
                setRangeInfo('Tanggal dari dan sampai wajib diisi.', true);
                return;
            }
            if (dateFrom > dateTo) {
                setRangeInfo('Rentang tanggal tidak valid.', true);
                return;
            }

            if (applyRangeBtn) applyRangeBtn.disabled = true;
            setRangeInfo('Memuat data historis...');
            if (historicalLoading) {
                historicalLoading.classList.remove('hidden');
                historicalLoading.classList.add('flex');
            }
            try {
                setMode('historical', { refresh: false });
                const payload = await fetchRmsHistory(dateFrom, dateTo);
                chartData = normalizeChartData(payload.rmsChartData || {});
                if (chartInstance) renderChart(chartType);
                setRangeInfo(`Histori ${dateFrom} s/d ${dateTo} (${payload.meta?.points ?? 0} titik)`);
            } catch (error) {
                setRangeInfo(error.message || 'Gagal memuat data historis.', true);
            } finally {
                if (applyRangeBtn) applyRangeBtn.disabled = false;
                if (historicalLoading) {
                    historicalLoading.classList.add('hidden');
                    historicalLoading.classList.remove('flex');
                }
            }
        }

        function resetToLiveRange() {
            setMode('live');
        }
        function downloadChart(mime, ext) {
            const link = document.createElement('a');
            link.href = chartInstance.toBase64Image(mime);
            link.download = 'grafik-rms-value.' + ext;
            link.click();
        }
        if (downloadBtn && downloadMenu) {
            downloadBtn.addEventListener('click', function (e) {
                downloadMenu.classList.toggle('hidden');
            });
            downloadMenu.querySelectorAll('button[data-format]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    const format = btn.getAttribute('data-format');
                    if (format === 'png') {
                        downloadChart('image/png', 'png');
                    } else if (format === 'jpg') {
                        downloadChart('image/jpeg', 'jpg');
                    }
                    downloadMenu.classList.add('hidden');
                });
            });
            // Hide menu if click outside
            document.addEventListener('click', function (e) {
                if (!downloadBtn.contains(e.target) && !downloadMenu.contains(e.target)) {
                    downloadMenu.classList.add('hidden');
                }
            });
        }

        window.updateDashboardRmsChart = function (nextChartData, options = {}) {
            const forceUpdate = Boolean(options && options.force);
            if (historyMode && !forceUpdate) {
                return;
            }
            chartData = normalizeChartData(nextChartData || {});
            if (chartInstance) renderChart(chartType);
        };

        window.isDashboardRmsHistoryMode = function () {
            return historyMode;
        };

        setDefaultDateInputs();
        setMode('live', { refresh: false });

        if (applyRangeBtn) {
            applyRangeBtn.addEventListener('click', applyHistoryRange);
        }
        if (resetRangeBtn) {
            resetRangeBtn.addEventListener('click', resetToLiveRange);
        }
        if (liveModeBtn) {
            liveModeBtn.addEventListener('click', resetToLiveRange);
        }
        if (historicalModeBtn) {
            historicalModeBtn.addEventListener('click', function () {
                setMode('historical', { refresh: false });
            });
        }
        if (resetZoomBtn) {
            resetZoomBtn.addEventListener('click', function () {
                if (chartInstance && typeof chartInstance.resetZoom === 'function') {
                    chartInstance.resetZoom();
                }
            });
        }
    });
</script>
