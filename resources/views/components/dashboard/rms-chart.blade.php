<!-- RMS Value Chart -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <h3 class="text-xl font-bold text-emerald-900 mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Grafik RMS Value Trend
    </h3>
    <div class="relative h-80">
        <canvas id="rmsChart" data-chart="{{ json_encode($rmsChartData ?? []) }}"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = JSON.parse(document.getElementById('rmsChart').dataset.chart);
        const ctx = document.getElementById('rmsChart').getContext('2d');
        let chartType = 'line';
        const chartTypeSelect = document.getElementById('chartType');
        let chartInstance = null;

        function renderChart(type) {
            if (chartInstance) chartInstance.destroy();
            chartInstance = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartData.labels || [],
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: chartData.values || [],
                            borderColor: '#059669',
                            backgroundColor: type === 'bar' ? 'rgba(5, 150, 105, 0.3)' : 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 2,
                            fill: type === 'line',
                            tension: type === 'line' ? 0.4 : undefined,
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
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return 'Waktu: ' + context[0].label;
                                },
                                label: function(context) {
                                    let rms = context.parsed.y;
                                    let label = 'RMS: ' + rms + ' mm/s';
                                    if (chartData.machines && chartData.machines[context.dataIndex]) {
                                        label += ' | Mesin: ' + chartData.machines[context.dataIndex];
                                    }
                                    return label;
                                }
                            }
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

        renderChart(chartType);
        if (chartTypeSelect) {
            chartTypeSelect.addEventListener('change', function(e) {
                chartType = e.target.value;
                renderChart(chartType);
            });
        }
    });
</script>

