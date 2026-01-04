<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900">Data Grafik</h2>
                <p class="text-sm text-green-600 font-medium">Visualisasi Data RMS Value</p>
            </div>
            <div class="text-sm text-gray-700 bg-gradient-to-br from-green-50 to-emerald-50 px-4 py-2.5 rounded-lg border-2 border-green-200 shadow-sm">
                <span class="font-bold" id="currentDate">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Panel -->
            <div class="bg-gradient-to-br from-white via-green-50/30 to-emerald-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6">
                <!-- View Mode Tabs -->
                <div class="flex flex-wrap gap-2 mb-6 pb-4 border-b-2 border-green-100">
                    <button id="singleViewBtn" class="px-4 py-2 text-sm font-bold text-white bg-green-600 rounded-lg shadow-lg hover:bg-green-700 active:bg-green-800 focus:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 transition border-2 border-green-600 cursor-pointer" style="min-width: 100px; appearance: none; -webkit-appearance: none;">
                        Satu Mesin
                    </button>
                    <button id="comparisonViewBtn" class="px-4 py-2 text-sm font-bold text-green-700 bg-green-100 rounded-lg hover:bg-green-200 active:bg-green-300 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition border-2 border-green-400 shadow-md cursor-pointer" style="min-width: 100px; appearance: none; -webkit-appearance: none;">
                        Perbandingan
                    </button>
                    <button id="heatmapViewBtn" class="px-4 py-2 text-sm font-bold text-green-700 bg-green-100 rounded-lg hover:bg-green-200 active:bg-green-300 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition border-2 border-green-400 shadow-md cursor-pointer" style="min-width: 120px; appearance: none; -webkit-appearance: none;">
                        Kalender Heatmap
                    </button>
                </div>

                <!-- Single Machine Filter -->
                <div id="singleMachineFilter" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Machine Selector -->
                    <div>
                        <label for="graphMachineSelector" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                            </svg>
                            <span>Pilih Mesin</span>
                        </label>
                        <select id="graphMachineSelector" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 font-medium bg-white shadow-sm transition hover:border-green-300">
                            <option value="">-- Pilih Mesin --</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->location }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="dateFrom" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Dari Tanggal</span>
                        </label>
                        <input type="date" id="dateFrom" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition hover:border-green-300" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="dateTo" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Sampai Tanggal</span>
                        </label>
                        <input type="date" id="dateTo" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition hover:border-green-300" value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <!-- Quick Presets & Actions -->
                    <div>
                        <label class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Jangka Waktu</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="timePreset" class="flex-1 px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 font-medium bg-white shadow-sm transition hover:border-green-300">
                                <option value="">Pilih Preset</option>
                                <option value="7">Terakhir 7 Hari</option>
                                <option value="14">Terakhir 14 Hari</option>
                                <option value="30">Terakhir 30 Hari</option>
                                <option value="60">Terakhir 60 Hari</option>
                                <option value="90">Terakhir 90 Hari</option>
                            </select>
                            <button id="applyFilterBtn" class="p-2.5 bg-green-100 text-green-700 font-bold rounded-lg hover:bg-green-200 hover:text-green-800 active:bg-green-300 transition-all duration-200 shadow-md hover:shadow-lg border-2 border-green-300 hover:border-green-400" title="Terapkan Filter">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </button>
                            <button id="exportCsvBtn" class="p-2.5 bg-green-100 text-green-700 font-bold rounded-lg hover:bg-green-200 hover:text-green-800 active:bg-green-300 transition-all duration-200 shadow-md hover:shadow-lg border-2 border-green-300 hover:border-green-400" title="Download CSV">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Comparison Filter (Hidden by default) -->
                <div id="comparisonFilter" class="hidden">
                    <!-- Compact Machine Selection with Action Buttons -->
                    <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
                        <!-- Machine Selection Grid -->
                        <div id="comparisonMachinesList" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 flex-1 min-w-0">
                            @foreach($machines as $machine)
                                <button type="button" class="comparison-machine-btn px-3 py-2.5 border-2 border-green-300 rounded-lg bg-white text-green-800 font-semibold hover:bg-green-50 hover:border-green-500 transition cursor-pointer text-xs line-clamp-1" value="{{ $machine->id }}" style="appearance: none; -webkit-appearance: none;">
                                    {{ $machine->name }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Date and Action Section -->
                        <div class="flex gap-3 items-center flex-shrink-0">
                            <!-- Date Range Picker Button -->
                            <button id="openDatePickerBtn" class="px-4 py-2.5 bg-white hover:bg-blue-50 text-blue-600 font-bold rounded-lg transition shadow-md cursor-pointer text-sm flex items-center space-x-2 border-2 border-blue-300 whitespace-nowrap" style="appearance: none; -webkit-appearance: none;">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span id="dateRangeText">Pilih Tanggal</span>
                            </button>

                            <!-- Load Button -->
                            <button id="loadComparisonBtn" class="px-4 py-2.5 bg-white hover:bg-gray-50 text-black font-bold rounded-lg transition shadow-md cursor-pointer text-sm flex items-center space-x-2 border-2 border-green-300 whitespace-nowrap" style="appearance: none; -webkit-appearance: none;">
                                <svg class="w-4 h-4 flex-shrink-0 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Muat Perbandingan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Date Inputs for Storage -->
                    <input type="date" id="compDateFrom" class="hidden" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                    <input type="date" id="compDateTo" class="hidden" value="{{ now()->format('Y-m-d') }}">

                    <!-- Selected Machines Info -->
                    <div id="selectedMachinesInfo" class="text-xs text-green-700 font-semibold px-3 py-2 bg-green-100 rounded-lg border border-green-300 hidden mt-3">
                        Mesin Terpilih: <span id="selectedCount">0</span>/5
                    </div>
                </div>

                <!-- Date Range Picker Modal -->
                <div id="datePickerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full mx-4 border-2 border-green-300">
                        <h3 class="text-lg font-bold text-green-900 mb-4">Pilih Rentang Tanggal</h3>

                        <div class="space-y-4">
                            <!-- Date From -->
                            <div>
                                <label for="modalDateFrom" class="block text-sm font-bold text-green-800 mb-2">Dari Tanggal</label>
                                <input type="date" id="modalDateFrom" class="w-full px-4 py-2.5 border-2 border-green-300 rounded-lg bg-white text-green-900 font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 transition hover:border-green-400" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="modalDateTo" class="block text-sm font-bold text-green-800 mb-2">Sampai Tanggal</label>
                                <input type="date" id="modalDateTo" class="w-full px-4 py-2.5 border-2 border-green-300 rounded-lg bg-white text-green-900 font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 transition hover:border-green-400" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex gap-2 mt-6">
                            <button id="cancelDatePickerBtn" class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-800 font-bold rounded-lg hover:bg-gray-300 transition cursor-pointer border-2 border-gray-300" style="appearance: none; -webkit-appearance: none;">
                                Batal
                            </button>
                            <button id="confirmDatePickerBtn" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-green-50 font-bold rounded-lg hover:from-green-700 hover:to-emerald-700 transition shadow-lg border-2 border-green-700 cursor-pointer" style="appearance: none; -webkit-appearance: none;">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Heatmap Filter (Hidden by default) -->
                <div id="heatmapFilter" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 max-w-3xl">
                        <!-- Machine Selection -->
                        <div>
                            <label for="heatmapMachine" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                                <span>Pilih Mesin</span>
                            </label>
                            <select id="heatmapMachine" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 text-sm font-medium bg-white shadow-sm transition hover:border-green-300">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->location }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Selection -->
                        <div>
                            <label for="heatmapMonth" class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Bulan</span>
                            </label>
                            <input type="month" id="heatmapMonth" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition hover:border-green-300" value="{{ now()->format('Y-m') }}">
                        </div>

                        <!-- Load Button Column -->
                        <div>
                            <label class="flex items-center space-x-1.5 text-sm font-semibold text-gray-700 mb-2 invisible">
                                <span>Action</span>
                            </label>
                            <button id="loadHeatmapBtn" class="w-full px-4 py-2.5 bg-white hover:bg-gray-50 text-black font-bold rounded-lg transition shadow-md cursor-pointer text-sm flex items-center justify-center space-x-2 border-2 border-green-300 whitespace-nowrap" style="appearance: none; -webkit-appearance: none;">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Muat Heatmap</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden p-5 bg-gradient-to-r from-green-50 via-emerald-50 to-green-50 border-l-4 border-green-500 rounded-lg flex items-center space-x-3 shadow-lg">
                <div class="animate-spin">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <span class="text-sm text-green-800 font-bold">Memuat data grafik...</span>
            </div>

            <!-- Trend Chart Section -->
            <div id="trendChartSection" class="bg-gradient-to-br from-white to-green-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6 hidden">
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-green-100">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Grafik Trend RMS Value</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center space-x-2 text-sm text-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-2 rounded-lg border-2 border-green-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span id="chartDateRange" class="font-semibold">-</span>
                        </div>
                        <button id="exportImageBtn" class="p-2 bg-green-100 text-green-700 font-bold rounded-lg hover:bg-green-200 transition shadow-md border-2 border-green-300" title="Unduh sebagai Gambar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative bg-gray-50 rounded-lg p-4" style="height: 450px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Comparison Chart Section -->
            <div id="comparisonChartSection" class="bg-gradient-to-br from-white via-green-50 to-emerald-50 rounded-xl shadow-2xl border-2 border-green-300 p-8 hidden">
                <div class="flex items-center justify-between mb-6 pb-5 border-b-3 border-green-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl shadow-lg">
                            <svg class="w-7 h-7 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-green-900">Perbandingan RMS Value</h3>
                            <p class="text-sm text-green-700">Analisis antar mesin</p>
                        </div>
                    </div>
                    <button id="exportCompImageBtn" class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 text-green-100 font-bold rounded-lg hover:from-green-600 hover:to-emerald-600 active:from-green-700 active:to-emerald-700 transition shadow-lg hover:shadow-xl border-2 border-green-600 cursor-pointer" style="appearance: none; -webkit-appearance: none;" title="Unduh sebagai Gambar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative bg-gradient-to-b from-green-50 to-white rounded-xl p-6 shadow-inner" style="height: 480px;">
                    <canvas id="comparisonChart"></canvas>
                </div>
            </div>

            <!-- Heatmap Section -->
            <div id="heatmapSection" class="bg-gradient-to-br from-white to-green-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6 hidden">
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-green-100">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Heatmap Calendar - Aktivitas RMS Value</h3>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div id="heatmapCalendar" class="overflow-x-auto"></div>
                    <div class="mt-4 flex items-center justify-center gap-2">
                        <span class="text-sm font-semibold text-gray-700">Intensitas:</span>
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 bg-green-100 border border-gray-300 rounded"></div>
                            <span class="text-xs text-gray-600">Rendah</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 bg-green-300 border border-gray-300 rounded"></div>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 bg-green-500 border border-gray-300 rounded"></div>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 bg-green-700 border border-gray-300 rounded"></div>
                            <span class="text-xs text-gray-600">Tinggi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Summary Section -->
            <div id="statsSection" class="bg-gradient-to-br from-white to-green-50/20 rounded-xl shadow-xl border-2 border-green-100 p-6 hidden">
                <div class="flex items-center space-x-3 mb-5 pb-4 border-b-2 border-green-100">
                    <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan Statistik</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold text-gray-800">Metrik</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Min</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Max</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Rata-rata</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Total Data</th>
                                <th class="px-6 py-4 text-right font-bold text-gray-800">Anomali</th>
                            </tr>
                        </thead>
                        <tbody id="statsTableBody">
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Silakan pilih mesin dan filter untuk melihat data statistik
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-white rounded-full shadow-md mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Data Grafik</h3>
                    <p class="text-gray-500 max-w-md">Pilih mesin dan atur rentang tanggal untuk menampilkan grafik trend RMS Value dan statistik data</p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.js"></script>
    <script>
        let trendChart = null;
        let comparisonChart = null;
        let currentView = 'single';
        let graphData = {
            labels: [],
            rmsValues: [],
            rawData: []
        };

        // View Mode Switcher
        document.getElementById('singleViewBtn').addEventListener('click', function() {
            switchView('single');
        });
        document.getElementById('comparisonViewBtn').addEventListener('click', function() {
            switchView('comparison');
        });
        document.getElementById('heatmapViewBtn').addEventListener('click', function() {
            switchView('heatmap');
        });

        function switchView(view) {
            currentView = view;

            // Reset all buttons to inactive state
            const singleBtn = document.getElementById('singleViewBtn');
            const compBtn = document.getElementById('comparisonViewBtn');
            const heatmapBtn = document.getElementById('heatmapViewBtn');

            // Set inactive style
            const inactiveStyle = 'min-width: 100px; appearance: none; -webkit-appearance: none;';
            const inactiveClass = 'px-4 py-2 text-sm font-bold text-green-700 bg-green-100 rounded-lg hover:bg-green-200 active:bg-green-300 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition border-2 border-green-400 shadow-md cursor-pointer';
            const activeClass = 'px-4 py-2 text-sm font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 active:bg-green-800 focus:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 transition border-2 border-green-600 shadow-lg cursor-pointer';
            const heatmapInactiveStyle = 'min-width: 120px; appearance: none; -webkit-appearance: none;';

            // Set all to inactive first
            singleBtn.className = inactiveClass;
            compBtn.className = inactiveClass;
            heatmapBtn.className = inactiveClass;

            singleBtn.setAttribute('style', inactiveStyle);
            compBtn.setAttribute('style', inactiveStyle);
            heatmapBtn.setAttribute('style', heatmapInactiveStyle);

            // Activate the selected button
            if (view === 'single') {
                singleBtn.className = activeClass;
                singleBtn.setAttribute('style', inactiveStyle + ' background-color: rgb(22, 163, 74) !important;');
                document.getElementById('singleMachineFilter').classList.remove('hidden');
                document.getElementById('comparisonFilter').classList.add('hidden');
                document.getElementById('heatmapFilter').classList.add('hidden');
                document.getElementById('trendChartSection').classList.add('hidden');
                document.getElementById('comparisonChartSection').classList.add('hidden');
                document.getElementById('heatmapSection').classList.add('hidden');
                document.getElementById('statsSection').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
            } else if (view === 'comparison') {
                compBtn.className = activeClass;
                compBtn.setAttribute('style', inactiveStyle + ' background-color: rgb(22, 163, 74) !important;');
                document.getElementById('singleMachineFilter').classList.add('hidden');
                document.getElementById('comparisonFilter').classList.remove('hidden');
                document.getElementById('heatmapFilter').classList.add('hidden');
                document.getElementById('trendChartSection').classList.add('hidden');
                document.getElementById('comparisonChartSection').classList.add('hidden');
                document.getElementById('heatmapSection').classList.add('hidden');
                document.getElementById('statsSection').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
            } else if (view === 'heatmap') {
                heatmapBtn.className = activeClass;
                heatmapBtn.setAttribute('style', heatmapInactiveStyle + ' background-color: rgb(22, 163, 74) !important;');
                document.getElementById('singleMachineFilter').classList.add('hidden');
                document.getElementById('comparisonFilter').classList.add('hidden');
                document.getElementById('heatmapFilter').classList.remove('hidden');
                document.getElementById('trendChartSection').classList.add('hidden');
                document.getElementById('comparisonChartSection').classList.add('hidden');
                document.getElementById('heatmapSection').classList.add('hidden');
                document.getElementById('statsSection').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
            }
        }

        // Machine button selection for comparison
        let selectedComparisonMachines = [];
        document.querySelectorAll('.comparison-machine-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const machineId = this.value;

                if (selectedComparisonMachines.includes(machineId)) {
                    selectedComparisonMachines = selectedComparisonMachines.filter(id => id !== machineId);
                    this.classList.remove('bg-green-600', 'text-green-50', 'border-green-600');
                    this.classList.add('bg-white', 'text-green-800', 'border-green-300');
                } else {
                    if (selectedComparisonMachines.length >= 5) {
                        alert('Maksimal 5 mesin untuk perbandingan');
                        return;
                    }
                    selectedComparisonMachines.push(machineId);
                    this.classList.remove('bg-white', 'text-green-800', 'border-green-300');
                    this.classList.add('bg-green-600', 'text-green-50', 'border-green-600');
                }

                // Update selected info
                const infoDiv = document.getElementById('selectedMachinesInfo');
                document.getElementById('selectedCount').textContent = selectedComparisonMachines.length;
                if (selectedComparisonMachines.length > 0) {
                    infoDiv.classList.remove('hidden');
                } else {
                    infoDiv.classList.add('hidden');
                }
            });
        });

        // Limit machine checkbox selection
        document.querySelectorAll('.machine-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checked = document.querySelectorAll('.machine-checkbox:checked');
                if (checked.length > 5) {
                    this.checked = false;
                    alert('Maksimal 5 mesin untuk perbandingan');
                }
            });
        });

        // Date Picker Modal Handler
        const datePickerModal = document.getElementById('datePickerModal');
        const openDatePickerBtn = document.getElementById('openDatePickerBtn');
        const cancelDatePickerBtn = document.getElementById('cancelDatePickerBtn');
        const confirmDatePickerBtn = document.getElementById('confirmDatePickerBtn');
        const dateRangeText = document.getElementById('dateRangeText');
        const modalDateFrom = document.getElementById('modalDateFrom');
        const modalDateTo = document.getElementById('modalDateTo');
        const compDateFrom = document.getElementById('compDateFrom');
        const compDateTo = document.getElementById('compDateTo');

        // Sync initial values
        modalDateFrom.value = compDateFrom.value;
        modalDateTo.value = compDateTo.value;
        updateDateRangeText();

        openDatePickerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            datePickerModal.classList.remove('hidden');
            // Sync current values to modal
            modalDateFrom.value = compDateFrom.value;
            modalDateTo.value = compDateTo.value;
        });

        cancelDatePickerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            datePickerModal.classList.add('hidden');
        });

        confirmDatePickerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!modalDateFrom.value || !modalDateTo.value) {
                alert('Pilih kedua tanggal');
                return;
            }
            if (modalDateFrom.value > modalDateTo.value) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return;
            }
            compDateFrom.value = modalDateFrom.value;
            compDateTo.value = modalDateTo.value;
            updateDateRangeText();
            datePickerModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        datePickerModal.addEventListener('click', function(e) {
            if (e.target === datePickerModal) {
                datePickerModal.classList.add('hidden');
            }
        });

        function updateDateRangeText() {
            const fromDate = new Date(compDateFrom.value);
            const toDate = new Date(compDateTo.value);
            const fromStr = fromDate.toLocaleDateString('id-ID');
            const toStr = toDate.toLocaleDateString('id-ID');
            dateRangeText.textContent = `${fromStr} - ${toStr}`;
        }

        // Update current date in header
        function updateCurrentDate() {
            const now = new Date();
            document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Time Preset Handler
        document.getElementById('timePreset').addEventListener('change', function() {
            const days = parseInt(this.value);
            if (days > 0) {
                const toDate = new Date();
                const fromDate = new Date();
                fromDate.setDate(toDate.getDate() - days);

                document.getElementById('dateTo').valueAsDate = toDate;
                document.getElementById('dateFrom').valueAsDate = fromDate;
            }
        });

        // Apply Filter Button
        document.getElementById('applyFilterBtn').addEventListener('click', function() {
            loadGraphData();
        });

        // Load Comparison Button
        document.getElementById('loadComparisonBtn').addEventListener('click', function() {
            loadComparisonData();
        });

        // Load Heatmap Button
        document.getElementById('loadHeatmapBtn').addEventListener('click', function() {
            loadHeatmapData();
        });

        // Export Image Buttons
        document.getElementById('exportImageBtn').addEventListener('click', function() {
            exportChartAsImage('trendChart', 'trend-chart');
        });

        document.getElementById('exportCompImageBtn').addEventListener('click', function() {
            exportChartAsImage('comparisonChart', 'comparison-chart');
        });

        function exportChartAsImage(chartId, filename) {
            const canvas = document.getElementById(chartId);
            if (!canvas) return;

            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = `${filename}-${new Date().getTime()}.png`;
            link.href = url;
            link.click();
        }

        // Load Comparison Data
        function loadComparisonData() {
            const checkedMachines = selectedComparisonMachines;
            const dateFrom = document.getElementById('compDateFrom').value;
            const dateTo = document.getElementById('compDateTo').value;

            if (checkedMachines.length === 0) {
                alert('Pilih minimal 1 mesin untuk dibandingkan');
                return;
            }

            if (!dateFrom || !dateTo) {
                alert('Pilih rentang tanggal');
                return;
            }

            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('comparisonChartSection').classList.add('hidden');

            // Fetch data for all selected machines
            const promises = checkedMachines.map(machineId =>
                fetch(`/api/machine/${machineId}/historical-trend?date_from=${dateFrom}&date_to=${dateTo}`)
                    .then(res => res.json())
            );

            Promise.all(promises)
                .then(results => {
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    renderComparisonChart(results, checkedMachines);
                    document.getElementById('comparisonChartSection').classList.remove('hidden');
                    document.getElementById('emptyState').classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error loading comparison data:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                });
        }

        // Render Comparison Chart
        function renderComparisonChart(results, machineIds) {
            const ctx = document.getElementById('comparisonChart');
            if (!ctx) return;

            if (comparisonChart) {
                comparisonChart.destroy();
            }

            // Calculate average RMS for each machine
            const labels = [];
            const avgValues = [];
            const maxValues = [];
            const colors = [
                'rgba(34, 197, 94, 0.8)',   // green
                'rgba(59, 130, 246, 0.8)',  // blue
                'rgba(239, 68, 68, 0.8)',   // red
                'rgba(234, 179, 8, 0.8)',   // yellow
                'rgba(168, 85, 247, 0.8)'   // purple
            ];

            results.forEach((result, index) => {
                if (result.success && result.data && result.data.length > 0) {
                    labels.push(result.machine_name || `Mesin ${machineIds[index]}`);
                    const rmsValues = result.data.map(d => parseFloat(d.rms_value || 0));
                    const avg = rmsValues.reduce((a, b) => a + b, 0) / rmsValues.length;
                    const max = Math.max(...rmsValues);
                    avgValues.push(avg.toFixed(4));
                    maxValues.push(max.toFixed(4));
                }
            });

            comparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Rata-rata RMS Value',
                            data: avgValues,
                            backgroundColor: colors.slice(0, labels.length),
                            borderColor: colors.slice(0, labels.length).map(c => c.replace('0.8', '1')),
                            borderWidth: 2
                        },
                        {
                            label: 'Maksimal RMS Value',
                            data: maxValues,
                            backgroundColor: colors.slice(0, labels.length).map(c => c.replace('0.8', '0.4')),
                            borderColor: colors.slice(0, labels.length).map(c => c.replace('0.8', '1')),
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' G';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'RMS Value (G)'
                            }
                        }
                    }
                }
            });
        }

        // Load Heatmap Data
        function loadHeatmapData() {
            const machineId = document.getElementById('heatmapMachine').value;
            const month = document.getElementById('heatmapMonth').value;

            if (!machineId) {
                alert('Pilih mesin terlebih dahulu');
                return;
            }

            if (!month) {
                alert('Pilih bulan');
                return;
            }

            const [year, monthNum] = month.split('-');
            const firstDay = `${year}-${monthNum}-01`;
            const lastDay = new Date(year, monthNum, 0).getDate();
            const lastDayStr = `${year}-${monthNum}-${lastDay.toString().padStart(2, '0')}`;

            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('heatmapSection').classList.add('hidden');

            fetch(`/api/machine/${machineId}/historical-trend?date_from=${firstDay}&date_to=${lastDayStr}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    if (data.success && data.data && data.data.length > 0) {
                        renderHeatmap(data.data, year, monthNum);
                        document.getElementById('heatmapSection').classList.remove('hidden');
                        document.getElementById('emptyState').classList.add('hidden');
                    } else {
                        document.getElementById('emptyState').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading heatmap data:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                });
        }

        // Render Heatmap
        function renderHeatmap(data, year, month) {
            const container = document.getElementById('heatmapCalendar');

            // Group data by date
            const dataByDate = {};
            data.forEach(item => {
                const date = item.timestamp.split(' ')[0];
                if (!dataByDate[date]) {
                    dataByDate[date] = [];
                }
                dataByDate[date].push(parseFloat(item.rms_value || 0));
            });

            // Calculate average for each date
            const avgByDate = {};
            let maxAvg = 0;
            Object.keys(dataByDate).forEach(date => {
                const values = dataByDate[date];
                const avg = values.reduce((a, b) => a + b, 0) / values.length;
                avgByDate[date] = avg;
                if (avg > maxAvg) maxAvg = avg;
            });

            // Get calendar info
            const daysInMonth = new Date(year, month, 0).getDate();
            const firstDayOfWeek = new Date(year, month - 1, 1).getDay();

            // Build calendar HTML
            let html = '<table class="w-full border-collapse">';
            html += '<thead><tr>';
            ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'].forEach(day => {
                html += `<th class="p-2 text-sm font-bold text-gray-700">${day}</th>`;
            });
            html += '</tr></thead><tbody><tr>';

            // Empty cells before first day
            for (let i = 0; i < firstDayOfWeek; i++) {
                html += '<td class="p-2"></td>';
            }

            // Calendar days
            let currentDay = firstDayOfWeek;
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${month.padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const avg = avgByDate[dateStr] || 0;
                const intensity = maxAvg > 0 ? (avg / maxAvg) : 0;

                let bgColor = 'bg-gray-100';
                if (intensity > 0) {
                    if (intensity < 0.25) bgColor = 'bg-green-100';
                    else if (intensity < 0.5) bgColor = 'bg-green-300';
                    else if (intensity < 0.75) bgColor = 'bg-green-500 text-white';
                    else bgColor = 'bg-green-700 text-white';
                }

                html += `<td class="p-1">
                    <div class="${bgColor} rounded p-2 text-center text-sm font-semibold border border-gray-300 hover:ring-2 hover:ring-green-500 cursor-pointer transition"
                         title="${dateStr}: ${avg.toFixed(4)} G">
                        ${day}
                    </div>
                </td>`;

                currentDay++;
                if (currentDay === 7 && day < daysInMonth) {
                    html += '</tr><tr>';
                    currentDay = 0;
                }
            }

            html += '</tr></tbody></table>';
            container.innerHTML = html;
        }

        // Load Graph Data
        function loadGraphData() {
            const machineId = document.getElementById('graphMachineSelector').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            if (!machineId) {
                alert('Silakan pilih mesin terlebih dahulu');
                return;
            }

            if (!dateFrom || !dateTo) {
                alert('Silakan pilih tanggal dari dan sampai');
                return;
            }

            // Show loading
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('trendChartSection').classList.add('hidden');
            document.getElementById('statsSection').classList.add('hidden');

            // Fetch data from API
            fetch(`/api/machine/${machineId}/historical-trend?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingIndicator').classList.add('hidden');

                    if (data.success && data.data && data.data.length > 0) {
                        document.getElementById('emptyState').classList.add('hidden');
                        processGraphData(data.data, data.machine_name, dateFrom, dateTo);
                        renderChart();
                        renderStatistics(data);
                        document.getElementById('trendChartSection').classList.remove('hidden');
                        document.getElementById('statsSection').classList.remove('hidden');
                    } else {
                        document.getElementById('emptyState').classList.remove('hidden');
                        document.getElementById('trendChartSection').classList.add('hidden');
                        document.getElementById('statsSection').classList.add('hidden');
                        console.log('Tidak ada data untuk tanggal yang dipilih');
                    }
                })
                .catch(error => {
                    console.error('Error loading graph data:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                });
        }

        // Process Graph Data
        function processGraphData(data, machineName, dateFrom, dateTo) {
            graphData.labels = [];
            graphData.rmsValues = [];
            graphData.rawData = data;

            // Group by date if too many points
            if (data.length > 100) {
                // Aggregate by day
                const aggregated = {};
                data.forEach(item => {
                    const date = item.timestamp.split(' ')[0];
                    if (!aggregated[date]) {
                        aggregated[date] = [];
                    }
                    aggregated[date].push(parseFloat(item.rms_value || 0));
                });

                Object.keys(aggregated).sort().forEach(date => {
                    const values = aggregated[date];
                    const avg = values.reduce((a, b) => a + b, 0) / values.length;
                    graphData.labels.push(date);
                    graphData.rmsValues.push(avg.toFixed(4));
                });
            } else {
                // Show each point
                data.forEach(item => {
                    const timestamp = item.timestamp.split(' ')[1] || item.timestamp;
                    graphData.labels.push(timestamp);
                    graphData.rmsValues.push(parseFloat(item.rms_value || 0).toFixed(4));
                });
            }

            // Update chart date range
            document.getElementById('chartDateRange').textContent = `${dateFrom} hingga ${dateTo}`;
        }

        // Render Chart
        function renderChart() {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;

            // Destroy existing chart
            if (trendChart) {
                trendChart.destroy();
            }

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: graphData.labels,
                    datasets: [
                        {
                            label: 'RMS Value',
                            data: graphData.rmsValues,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: graphData.labels.length > 50 ? 0 : 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
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
                            labels: {
                                padding: 15,
                                font: { size: 13, weight: 'bold' },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                afterLabel: function(context) {
                                    return 'G-Force';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'RMS Value (G)'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        // Render Statistics
        function renderStatistics(data) {
            const tbody = document.getElementById('statsTableBody');

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data</td></tr>';
                return;
            }

            const rmsValues = data.data.map(item => parseFloat(item.rms_value || 0));
            const anomalyCount = data.data.filter(item => item.is_anomaly === 1).length;

            const min = Math.min(...rmsValues).toFixed(4);
            const max = Math.max(...rmsValues).toFixed(4);
            const avg = (rmsValues.reduce((a, b) => a + b, 0) / rmsValues.length).toFixed(4);

            tbody.innerHTML = `
                <tr class="border-b border-gray-200 hover:bg-blue-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-900">RMS Value</td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-green-100 text-green-800 font-mono text-sm font-semibold rounded-lg">${min} G</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-red-100 text-red-800 font-mono text-sm font-semibold rounded-lg">${max} G</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 font-mono text-sm font-semibold rounded-lg">${avg} G</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 font-mono text-sm font-semibold rounded-lg">${rmsValues.length}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-4 py-1.5 rounded-full text-sm font-bold shadow-sm ${anomalyCount > 0 ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'}">
                            ${anomalyCount}
                        </span>
                    </td>
                </tr>
            `;
        }

        // Export to CSV
        document.getElementById('exportCsvBtn').addEventListener('click', function() {
            if (graphData.rawData.length === 0) {
                alert('Tidak ada data untuk di-export');
                return;
            }

            const machineId = document.getElementById('graphMachineSelector').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            let csv = 'Timestamp,RMS Value (G),Peak Amplitude,Dominant Frequency,Status\n';

            graphData.rawData.forEach(item => {
                const row = [
                    item.timestamp,
                    item.rms_value || '0',
                    item.peak_amplitude || '0',
                    item.dominant_frequency || '0',
                    item.is_anomaly === 1 ? 'ANOMALI' : 'NORMAL'
                ];
                csv += row.map(val => `"${val}"`).join(',') + '\n';
            });

            // Download
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            const fileName = `grafik-rms-${machineId}-${dateFrom}-${dateTo}.csv`;

            link.setAttribute('href', url);
            link.setAttribute('download', fileName);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentDate();
            // Set initial view to 'single'
            switchView('single');
        });
    </script>
</x-app-layout>
