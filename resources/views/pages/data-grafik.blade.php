
<x-app-layout>
	<x-slot name="header">
		<h2 class="font-bold text-2xl text-gray-900">Data Grafik Mesin</h2>
		<p class="text-sm text-green-600 font-medium">Visualisasi Data Sensor Mesin</p>
	</x-slot>

	<div class="py-8">
		<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<!-- Filter Panel -->
			<div class="bg-white rounded-xl shadow-lg border p-6 mb-6">
				<form id="filterForm" class="flex flex-row justify-around items-end gap-4 flex-wrap">
					<!-- Mesin Selector -->
					<div>
						<label for="machineSelector" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Mesin</label>
						<select id="machineSelector" name="machine_id" class="w-full px-4 py-2 border rounded-lg">
							<option value="">-- Pilih Mesin --</option>
							@foreach($machines ?? [] as $machine)
								<option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->location }})</option>
							@endforeach
						</select>
					</div>
					<!-- Tanggal Dari -->
					<div>
						<label for="dateFrom" class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
						<input type="text" id="dateFrom" name="date_from" class="w-full px-4 py-2 border rounded-lg" value="{{ \Carbon\Carbon::parse($latestDate ?? '2025-12-24')->format('d/m/Y') }}">
					</div>
					<!-- Tanggal Sampai -->
					<div>
						<label for="dateTo" class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
						<input type="text" id="dateTo" name="date_to" class="w-full px-4 py-2 border rounded-lg" value="{{ \Carbon\Carbon::parse($latestDate ?? '2025-12-24')->format('d/m/Y') }}">
					</div>
					<!-- Tombol Filter -->
					<div>
						<button type="submit" id="applyFilterBtn" class="w-full px-4 py-2 font-bold rounded-lg border border-green-700 shadow transition text-white mt-6" style="background-color:#16A34A; color:#fff;">Terapkan Filter</button>
					</div>
				</form>
			</div>

			<!-- Grafik Utama -->
			<div class="bg-white rounded-xl shadow-lg border p-6" style="height: 350px;">
				<h3 class="text-lg font-bold mb-4">Grafik RMS Value</h3>
				<canvas id="mainChart" style="height: 100%; width: 100%;"></canvas>
				<div id="loadingIndicator" class="mt-4 text-center hidden">
					<span class="text-green-600 font-semibold">Memuat data grafik...</span>
				</div>
				<div id="emptyState" class="mt-4 text-center hidden">
					<span class="text-gray-500">Tidak ada data untuk filter yang dipilih.</span>
				</div>
			</div>
		</div>
	</div>

	<!-- Chart.js CDN -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
		<!-- Flatpickr Datepicker -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script>
					function toApiDateFormat(dateStr) {
						if (!dateStr) return '';
						const [day, month, year] = dateStr.split('/');
						return `${year}-${month}-${day}`;
					}
			flatpickr('#dateFrom', {
				dateFormat: 'd/m/Y',
				defaultDate: document.getElementById('dateFrom').value
			});
			flatpickr('#dateTo', {
				dateFormat: 'd/m/Y',
				defaultDate: document.getElementById('dateTo').value
			});
		document.addEventListener('DOMContentLoaded', function() {
			const applyFilterBtn = document.getElementById('applyFilterBtn');
			const machineSelector = document.getElementById('machineSelector');
			const dateFrom = document.getElementById('dateFrom');
			const dateTo = document.getElementById('dateTo');
			const mainChartCanvas = document.getElementById('mainChart');
			const loadingIndicator = document.getElementById('loadingIndicator');
			const emptyState = document.getElementById('emptyState');
			let mainChart = null;

			function showLoading(show) {
				loadingIndicator.classList.toggle('hidden', !show);
			}
			function showEmpty(show) {
				emptyState.classList.toggle('hidden', !show);
			}

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
							pointRadius: 3,
							pointHoverRadius: 5,
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
							},
							tooltip: {
								backgroundColor: '#fff',
								titleColor: '#16A34A',
								bodyColor: '#222',
								borderColor: '#16A34A',
								borderWidth: 1
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
									maxTicksLimit: 10,
									callback: function(value, index, values) {
										// Tampilkan hanya jam:menit
										const label = this.getLabelForValue(value);
										const parts = label.split(' ');
										return parts.length > 2 ? parts[2] : label;
									}
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
				const from = toApiDateFormat(dateFrom.value);
				const to = toApiDateFormat(dateTo.value);

				if (!machineId || !from || !to) {
					alert('Pilih mesin dan rentang tanggal terlebih dahulu!');
					return;
				}

				showLoading(true);
				showEmpty(false);

				fetch(`/api/machine/${machineId}/historical-trend?date_from=${from}&date_to=${to}`)
					.then(res => res.json())
					.then(data => {
						showLoading(false);
						if (data.success && data.data && data.data.length > 0) {
							const labels = data.data.map(item => item.timestamp);
							const rmsValues = data.data.map(item => parseFloat(item.rms_value || 0));
							renderChart(labels, rmsValues);
							showEmpty(false);
						} else {
							showEmpty(true);
							if (mainChart) mainChart.destroy();
						}
					})
					.catch(err => {
						showLoading(false);
						showEmpty(true);
						if (mainChart) mainChart.destroy();
					});
			}

			// Prevent form submit and use AJAX only
			document.getElementById('filterForm').addEventListener('submit', loadChartData);
			applyFilterBtn.addEventListener('click', loadChartData);
		});
	</script>
</x-app-layout>
