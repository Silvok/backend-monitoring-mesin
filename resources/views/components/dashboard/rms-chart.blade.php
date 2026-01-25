<!-- RMS Value Chart -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-8">
    <h3 class="text-lg sm:text-xl font-bold text-emerald-900 mb-4 sm:mb-6 flex items-center">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        {{ __('messages.dashboard.rms_trend') }}
    </h3>
    <div class="flex items-center justify-between mb-3">
        <label class="flex items-center gap-2 text-xs text-gray-600">
            <input id="rmsScaleToggle" type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" checked>
            {{ __('messages.dashboard.auto_zoom') }}
        </label>
        <span class="text-[11px] text-gray-400">{{ __('messages.dashboard.full_scale') }}</span>
    </div>
    <div class="relative h-64 sm:h-80">
        <canvas id="rmsChart" data-chart="{{ json_encode($rmsChartData ?? []) }}"></canvas>
    </div>
    <div class="flex mt-4 justify-end">
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
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = JSON.parse(document.getElementById('rmsChart').dataset.chart);
        const ctx = document.getElementById('rmsChart').getContext('2d');
        let chartType = 'line';
        const chartTypeSelect = document.getElementById('chartType');
        const scaleToggle = document.getElementById('rmsScaleToggle');
        let chartInstance = null;
        let useAutoScale = true;

        function renderChart(type) {
            if (chartInstance) chartInstance.destroy();
            // Highlight anomaly/critical/fault points
            const statusArr = chartData.statuses || [];
            const highlightStatuses = ['ANOMALY', 'FAULT', 'CRITICAL', 'WARNING'];
            const values = chartData.values || [];
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
            const barColors = (chartData.values || []).map((_, i) => {
                if (highlightStatuses.includes((statusArr[i] || '').toUpperCase())) {
                    return 'rgba(239,68,68,0.7)'; // merah
                }
                return 'rgba(5,150,105,0.3)'; // hijau
            });
            const pointColors = (chartData.values || []).map((_, i) => {
                if (highlightStatuses.includes((statusArr[i] || '').toUpperCase())) {
                    return '#ef4444'; // merah
                }
                return '#059669'; // hijau
            });
            chartInstance = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartData.labels || [],
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: chartData.values || [],
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
                        tooltip: {
                            callbacks: {
                                title: function (context) {
                                    const idx = context[0].dataIndex;
                                    let waktu = chartData.full_times && chartData.full_times[idx] ? chartData.full_times[idx] : context[0].label;
                                    return '{{ __('messages.dashboard.time') }}: ' + waktu;
                                },
                                label: function (context) {
                                    const idx = context.dataIndex;
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
                            grid: {
                                color: '#e5e7eb'
                            },
                            ticks: {
                                color: '#6b7280',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10
                            }
                        }
                    }
                }
            });
        }

        renderChart(chartType);

        if (scaleToggle) {
            scaleToggle.addEventListener('change', function () {
                useAutoScale = scaleToggle.checked;
                renderChart(chartType);
            });
        }

        if (chartTypeSelect) {
            chartTypeSelect.addEventListener('change', function (e) {
                chartType = e.target.value;
                renderChart(chartType);
            });
        }

        // Download button with popup menu
        const downloadBtn = document.getElementById('downloadBtn');
        const downloadMenu = document.getElementById('downloadMenu');
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
    });
</script>
