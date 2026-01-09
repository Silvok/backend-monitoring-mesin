
<x-app-layout>
	<x-slot name="header">
		<h2 class="font-bold text-2xl text-gray-900">Data Grafik Mesin</h2>
		<p class="text-sm text-green-600 font-medium">Visualisasi Data Sensor Mesin</p>
	</x-slot>

		<div class="py-8">
			<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
				<!-- Filter Panel -->
				<div class="bg-white rounded-xl shadow-md p-6 mb-8 flex items-center">
					<form id="filterForm" class="flex w-full items-center gap-4">
						<!-- Mesin Selector -->
						<div class="flex flex-col w-1/3">
							<label for="machineSelector" class="block text-sm font-bold text-gray-700 mb-2">Pilih Mesin</label>
							<select id="machineSelector" name="machine_id" class="w-full h-12 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white shadow-sm transition">
								<option value="">-- Pilih Mesin --</option>
								@foreach($machines ?? [] as $machine)
									<option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->location }})</option>
								@endforeach
							</select>
						</div>
						<!-- Tanggal Dari -->
						<div class="flex flex-col w-1/4">
							<label for="dateFrom" class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal</label>
							<input type="text" id="dateFrom" name="date_from" class="w-full h-12 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white shadow-sm transition" value="{{ \Carbon\Carbon::parse($latestDate ?? '2025-12-24')->format('d/m/Y') }}">
						</div>
						<!-- Tanggal Sampai -->
						<div class="flex flex-col w-1/4">
							<label for="dateTo" class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
							<input type="text" id="dateTo" name="date_to" class="w-full h-12 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white shadow-sm transition" value="{{ \Carbon\Carbon::parse($latestDate ?? '2025-12-24')->format('d/m/Y') }}">
						</div>
						<!-- Tombol Filter -->
						<div class="flex items-end w-1/5">
							<button type="submit" id="applyFilterBtn" class="w-full h-12 px-4 font-bold rounded-lg border-2 shadow transition text-white mt-6" style="background-color:#16a34a; border-color:#15803d;">Terapkan Filter</button>
						</div>
					</form>
				</div>

				<!-- Grafik Utama -->
				<div class="bg-white rounded-xl shadow-md p-6">
					<h3 class="text-xl font-bold text-gray-700 mb-4">Grafik RMS Value</h3>
					<div class="w-full h-72 flex items-center justify-center">
						<canvas id="mainChart" style="height: 100%; width: 100%;"></canvas>
					</div>
				</div>
			</div>
		</div>
</x-app-layout>
