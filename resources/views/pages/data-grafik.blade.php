<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl" style="color: #185519;">
                    Data Grafik
                </h2>
                <!-- Live Status Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full border" style="background-color: #f0faf3; border-color: #b3e5c0;">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #2bc970;"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" style="background-color: #118B50;"></span>
                    </div>
                    <span class="text-xs font-semibold" style="color: #185519;">Live</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
                </div>
                <button onclick="refreshDashboard()" aria-label="Refresh Data Grafik" class="px-4 py-1.5 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2" style="background-color: #118B50;" onmouseover="this.style.backgroundColor='#185519'" onmouseout="this.style.backgroundColor='#118B50'">
                    <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <!-- Filter Form: Tanggal & Mesin -->
            <form method="GET" action="" class="mb-8 bg-white rounded-xl shadow p-4 sm:p-6 flex flex-wrap items-end gap-3 sm:gap-4">
                <div class="w-full sm:w-auto flex-1 min-w-[160px]">
                                    <label for="condition_status" class="block text-sm font-semibold text-emerald-900 mb-1">Status/Condition</label>
                                    <select name="condition_status" id="condition_status" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">Semua Status</option>
                                        <option value="NORMAL" {{ request('condition_status') == 'NORMAL' ? 'selected' : '' }}>NORMAL</option>
                                        <option value="ANOMALY" {{ request('condition_status') == 'ANOMALY' ? 'selected' : '' }}>ANOMALY</option>
                                        <option value="WARNING" {{ request('condition_status') == 'WARNING' ? 'selected' : '' }}>WARNING</option>
                                        <option value="FAULT" {{ request('condition_status') == 'FAULT' ? 'selected' : '' }}>FAULT</option>
                                        <option value="CRITICAL" {{ request('condition_status') == 'CRITICAL' ? 'selected' : '' }}>CRITICAL</option>
                                    </select>
                                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[160px]">
                    <label for="machine_id" class="block text-sm font-semibold text-emerald-900 mb-1">Mesin</label>
                    <select name="machine_id" id="machine_id" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none" aria-label="Pilih Mesin">
                        <option value="">Semua Mesin</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ request('machine_id') == $machine->id ? 'selected' : '' }}>{{ $machine->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[160px]">
                    <label for="start_date" class="block text-sm font-semibold text-emerald-900 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none" aria-label="Tanggal Mulai"
                        min="{{ $earliestDate ? \Carbon\Carbon::parse($earliestDate)->format('Y-m-d') : '' }}"
                        max="{{ $latestDate ? \Carbon\Carbon::parse($latestDate)->format('Y-m-d') : '' }}"
                        value="{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d') : ($earliestDate ? \Carbon\Carbon::parse($earliestDate)->format('Y-m-d') : '') }}">
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[160px]">
                    <label for="end_date" class="block text-sm font-semibold text-emerald-900 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none" aria-label="Tanggal Akhir"
                        min="{{ $earliestDate ? \Carbon\Carbon::parse($earliestDate)->format('Y-m-d') : '' }}"
                        max="{{ $latestDate ? \Carbon\Carbon::parse($latestDate)->format('Y-m-d') : '' }}"
                        value="{{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('Y-m-d') : ($latestDate ? \Carbon\Carbon::parse($latestDate)->format('Y-m-d') : '') }}">
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[160px]">
                    <label for="aggregation_interval" class="block text-sm font-semibold text-emerald-900 mb-1">Interval Agregasi</label>
                    <select name="aggregation_interval" id="aggregation_interval" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none" aria-label="Interval Agregasi">
                        <option value="1" {{ request('aggregation_interval', '3') == '1' ? 'selected' : '' }}>1 Menit</option>
                        <option value="3" {{ request('aggregation_interval', '3') == '3' ? 'selected' : '' }}>3 Menit</option>
                        <option value="5" {{ request('aggregation_interval', '3') == '5' ? 'selected' : '' }}>5 Menit</option>
                        <option value="10" {{ request('aggregation_interval', '3') == '10' ? 'selected' : '' }}>10 Menit</option>
                        <option value="15" {{ request('aggregation_interval', '3') == '15' ? 'selected' : '' }}>15 Menit</option>
                    </select>
                </div>
                <div class="flex items-end h-full w-full sm:w-auto">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow mt-6 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2" aria-label="Terapkan Filter">Terapkan Filter</button>
                </div>
            </form>
                                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
                        <!-- Grafik RMS Value Trend (24 Jam Terakhir) Card (Sama seperti dashboard) -->
                        <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4">
                            <label for="chartType" class="font-semibold text-emerald-900">Tipe Grafik:</label>
                            <div class="relative w-32 sm:w-48">
                                <select id="chartType" class="appearance-none border border-gray-300 rounded focus:ring-emerald-500 focus:border-emerald-500 pl-3 pr-6 py-1 text-sm font-semibold text-emerald-900 bg-white shadow w-full focus:outline-none" aria-label="Tipe Grafik">
                                    <option value="line">LINE</option>
                                    <option value="bar">BAR</option>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                                </span>
                            </div>
                        </div>
                        <div class="overflow-x-auto rounded-xl">
                            @component('components.dashboard.rms-chart', compact('rmsChartData'))
                            @endcomponent
                        </div>
                        @if(empty($rmsChartData['labels']))
                            <div class="bg-red-50 text-red-700 rounded-lg px-4 py-3 mt-4 text-center font-semibold">
                                Tidak ada data anomaly ditemukan untuk filter ini.
                            </div>
                        @endif
            <!-- Alert Panel -->
            <div id="alertPanel" class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden border-l-4 border-red-500" style="display: none;">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">
                                Alert Panel - Anomali Terdeteksi
                            </h3>
                            <span id="alertCount" class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-bold">0</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleAlertSound()" id="soundToggle" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                                <svg id="soundIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                </svg>
                                <span id="soundText">On</span>
                            </button>
                            <button onclick="dismissAllAlerts()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                Dismiss All
                            </button>
                            <button onclick="toggleAlertPanel()" class="bg-white/20 hover:bg-white/30 text-white p-1.5 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
        </div>
    </div>
</x-app-layout>
