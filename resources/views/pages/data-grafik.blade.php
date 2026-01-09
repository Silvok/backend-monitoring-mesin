</div>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const applyFilterBtn = document.getElementById('applyFilterBtn');
	const machineSelector = document.getElementById('machineSelector');
	const dateFrom = document.getElementById('dateFrom');
	const dateTo = document.getElementById('dateTo');
	const mainChartCanvas = document.getElementById('mainChart');
	let mainChart = null;

	function renderChart(labels, data) {
		if (mainChart) mainChart.destroy();
		mainChart = new Chart(mainChartCanvas, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					label: 'RMS Value',
					data: data,
					borderColor: 'rgba(34,197,94,1)',
					backgroundColor: 'rgba(34,197,94,0.15)',
					pointBackgroundColor: 'rgba(34,197,94,1)',
					pointBorderColor: '#fff',
					pointRadius: 2,
					pointHoverRadius: 4,
					fill: true,
					tension: 0.4
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'top',
						labels: {
							font: { size: 16, family: 'inherit', weight: 'bold' },
							color: '#16A34A'
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						title: { display: true, text: 'RMS Value (G)', font: { size: 14, weight: 'bold' } },
						ticks: { color: '#222', font: { size: 12 } },
						grid: { color: '#e5e7eb' }
					},
					x: {
						title: { display: true, text: 'Waktu', font: { size: 14, weight: 'bold' } },
						ticks: {
							color: '#222',
							font: { size: 11 },
							autoSkip: true,
							maxRotation: 0,
							minRotation: 0,
							maxTicksLimit: 10
						},
						grid: { color: '#e5e7eb' }
					}
				}
			}
		});
	}

	function loadChartData(e) {
		if (e) e.preventDefault();
		const machineId = machineSelector.value;
		const from = dateFrom.value.split('/').reverse().join('-');
		const to = dateTo.value.split('/').reverse().join('-');
		if (!machineId || !from || !to) {
			alert('Pilih mesin dan rentang tanggal terlebih dahulu!');
			return;
		}
		fetch(`/api/machine/${machineId}/historical-trend?date_from=${from}&date_to=${to}`)
			.then(res => res.json())
			.then(data => {
				if (data.success && data.data && data.data.length > 0) {
					const labels = data.data.map(item => item.timestamp);
					const rmsValues = data.data.map(item => parseFloat(item.rms_value || 0));
					renderChart(labels, rmsValues);
				} else {
					if (mainChart) mainChart.destroy();
				}
			})
			.catch(err => {
				if (mainChart) mainChart.destroy();
			});
	}

	document.getElementById('filterForm').addEventListener('submit', loadChartData);
	applyFilterBtn.addEventListener('click', loadChartData);
});
</script>

<x-app-layout>
	<x-slot name="header">
		<h2 class="font-bold text-2xl text-gray-900">Data Grafik Mesin</h2>
		<p class="text-sm text-green-600 font-medium">Visualisasi Data Sensor Mesin</p>
	</x-slot>

		<!-- Seluruh isi page data grafik mesin dihapus -->
</x-app-layout>
