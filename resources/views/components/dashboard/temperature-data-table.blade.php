<!-- Latest Temperature Data Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4 rounded-t-xl">
        <h3 class="text-xl font-bold text-white flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Data Suhu Terbaru
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Suhu (°C)</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($latestTemperatureData->take(5) as $data)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $data->recorded_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-900">
                        {{ $data->machine->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold
                        @if($data->temperature_c > 50) text-red-600
                        @elseif($data->temperature_c > 40) text-orange-600
                        @else text-green-600
                        @endif">
                        {{ number_format($data->temperature_c, 2) }}°C
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($data->temperature_c > 50)
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Tinggi</span>
                        @elseif($data->temperature_c > 40)
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800">Sedang</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Normal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        Tidak ada data suhu
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
