<!-- Latest Sensor Data Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-800 px-4 sm:px-6 py-4 rounded-t-xl">
        <h3 class="text-lg sm:text-xl font-bold text-white flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            {{ __('messages.dashboard.latest_sensor') }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.dashboard.time') }}</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.dashboard.machine') }}</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AX (g)</th>
                    <th class="hidden sm:table-cell px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AY (g)</th>
                    <th class="hidden md:table-cell px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AZ (g)</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.dashboard.temperature') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($latestSensorData->take(5) as $data)
                @php
                    // Find corresponding temperature reading for this sensor data
                    $tempReading = $latestTemperatureData->where('machine_id', $data->machine_id)
                        ->sortByDesc('recorded_at')
                        ->first();
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $data->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-semibold text-emerald-900">
                        {{ $data->machine->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($data->ax_g, 4) }}
                    </td>
                    <td class="hidden sm:table-cell px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($data->ay_g, 4) }}
                    </td>
                    <td class="hidden md:table-cell px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($data->az_g, 4) }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-semibold
                        @if($tempReading && $tempReading->temperature_c > 50) text-red-600
                        @elseif($tempReading && $tempReading->temperature_c > 40) text-orange-600
                        @elseif($tempReading) text-green-600
                        @else text-gray-500
                        @endif">
                        {{ $tempReading ? number_format($tempReading->temperature_c, 2) . '°C' : 'N/A' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        {{ __('messages.dashboard.no_sensor_data') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
