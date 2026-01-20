<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">
                    Manajemen Alert
                </h2>
                <!-- Active Alerts Indicator -->
                <div id="alertIndicator" class="flex items-center space-x-2 px-3 py-1.5 bg-yellow-50 rounded-full border border-yellow-200">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-yellow-700" id="activeAlertCount">Loading...</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="exportAlerts()" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </button>
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button onclick="switchTab('overview')" id="tab-overview"
                            class="tab-btn active px-6 py-4 text-sm font-medium border-b-2 border-emerald-500 text-emerald-600 bg-emerald-50/50">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Overview
                        </button>
                        <button onclick="switchTab('alerts')" id="tab-alerts"
                            class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Daftar Alert
                        </button>
                        <button onclick="switchTab('history')" id="tab-history"
                            class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat
                        </button>
                        <button onclick="switchTab('settings')" id="tab-settings"
                            class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content: Overview -->
            <div id="content-overview" class="tab-content">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Total Alerts Today -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Alert Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-900" id="statToday">-</p>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Sejak pukul 00:00</p>
                    </div>

                    <!-- Critical Alerts (Bahaya) -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Alert Bahaya</p>
                                <p class="text-3xl font-bold text-red-600" id="statCritical">-</p>
                            </div>
                            <div class="p-3 bg-red-50 rounded-xl">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">RMS ≥ <span id="criticalThreshold">4.5</span> mm/s</p>
                    </div>

                    <!-- Warning Alerts -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Alert Peringatan</p>
                                <p class="text-3xl font-bold text-yellow-600" id="statWarning">-</p>
                            </div>
                            <div class="p-3 bg-yellow-50 rounded-xl">
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">RMS ≥ <span id="warningThreshold">1.8</span> mm/s</p>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Alerts by Machine -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 overflow-hidden" style="max-height: 350px;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Alert per Mesin (24 Jam)</h3>
                        <div style="position: relative; height: 250px; width: 100%;">
                            <canvas id="alertsByMachineChart"></canvas>
                        </div>
                    </div>

                    <!-- Alerts by Severity -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 overflow-hidden" style="max-height: 350px;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Severity</h3>
                        <div style="position: relative; height: 250px; width: 100%;">
                            <canvas id="alertsBySeverityChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Acknowledgment Status -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Status Acknowledgment</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-green-800">Acknowledged</span>
                                </div>
                                <span class="text-2xl font-bold text-green-600" id="statAcknowledged">-</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-red-100 rounded-lg">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-red-800">Unacknowledged</span>
                                </div>
                                <span class="text-2xl font-bold text-red-600" id="statUnacknowledged">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Critical Alerts -->
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Alert Terbaru</h3>
                            <button onclick="switchTab('alerts')" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                                Lihat Semua →
                            </button>
                        </div>
                        <div class="space-y-3" id="recentAlertsList">
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>Memuat data alert...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Alerts List -->
            <div id="content-alerts" class="tab-content hidden">
                <!-- Filters -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mesin</label>
                            <select id="filterMachine" onchange="loadAlerts()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Semua Mesin</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                            <select id="filterSeverity" onchange="loadAlerts()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Semua Level</option>
                                <option value="critical">Bahaya</option>
                                <option value="warning">Peringatan</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" id="filterDateFrom" onchange="loadAlerts()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" id="filterDateTo" onchange="loadAlerts()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                            <button type="button" onclick="bulkAcknowledge()" class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Acknowledge Terpilih</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alerts Table -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mesin</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">RMS (mm/s)</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Severity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="alertsTableBody">
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                                        Memuat data alert...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-500" id="paginationInfo">
                            Menampilkan 0 dari 0 alert
                        </div>
                        <div class="flex space-x-2" id="paginationControls">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: History -->
            <div id="content-history" class="tab-content hidden">
                <!-- History Filters -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mesin</label>
                            <select id="historyFilterMachine" onchange="loadHistory()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Semua Mesin</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" id="historyDateFrom" onchange="loadHistory()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" id="historyDateTo" onchange="loadHistory()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                            <button type="button" onclick="loadHistory()" class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mesin</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">RMS (mm/s)</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Severity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Terjadi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Diakui Oleh</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Akui</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="historyTableBody">
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                        Memuat riwayat alert...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- History Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-500" id="historyPaginationInfo">
                            Menampilkan 0 dari 0 riwayat
                        </div>
                        <div class="flex space-x-2" id="historyPaginationControls">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Settings -->
            <div id="content-settings" class="tab-content hidden">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Per-Machine Threshold Configuration -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-emerald-50 rounded-lg">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Konfigurasi Threshold Per-Mesin</h3>
                                    <p class="text-sm text-gray-500">Berdasarkan standar ISO 10816-3</p>
                                </div>
                            </div>
                            <button onclick="loadMachineThresholds()" class="px-3 py-1.5 text-sm text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                        </div>

                        <!-- Machine Threshold Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Mesin</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Lokasi</th>
                                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Daya Motor</th>
                                        <th class="text-center py-3 px-4 font-semibold text-gray-700">ISO Class</th>
                                        <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                            <span class="inline-flex items-center">
                                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 mr-1.5"></span>
                                                Warning
                                            </span>
                                        </th>
                                        <th class="text-center py-3 px-4 font-semibold text-gray-700">
                                            <span class="inline-flex items-center">
                                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 mr-1.5"></span>
                                                Critical
                                            </span>
                                        </th>
                                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="machineThresholdTable" class="divide-y divide-gray-100">
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-gray-400">
                                            <svg class="w-8 h-8 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Memuat data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- ISO 10816-3 Reference -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Referensi ISO 10816-3 Threshold</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                                <div class="p-3 bg-white rounded-lg border border-gray-200">
                                    <div class="font-semibold text-gray-700 mb-1">Class I</div>
                                    <div class="text-gray-500 mb-2">Motor ≤ 15 kW (20 HP)</div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between"><span class="text-yellow-600">Warning:</span> <span>1.8 mm/s</span></div>
                                        <div class="flex justify-between"><span class="text-red-600">Critical:</span> <span>4.5 mm/s</span></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-lg border border-gray-200">
                                    <div class="font-semibold text-gray-700 mb-1">Class II</div>
                                    <div class="text-gray-500 mb-2">Motor 15-75 kW (20-100 HP)</div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between"><span class="text-yellow-600">Warning:</span> <span>2.8 mm/s</span></div>
                                        <div class="flex justify-between"><span class="text-red-600">Critical:</span> <span>7.1 mm/s</span></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-lg border border-gray-200">
                                    <div class="font-semibold text-gray-700 mb-1">Class III</div>
                                    <div class="text-gray-500 mb-2">Motor 75-300 kW (100-400 HP)</div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between"><span class="text-yellow-600">Warning:</span> <span>4.5 mm/s</span></div>
                                        <div class="flex justify-between"><span class="text-red-600">Critical:</span> <span>11.2 mm/s</span></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-lg border border-gray-200">
                                    <div class="font-semibold text-gray-700 mb-1">Class IV</div>
                                    <div class="text-gray-500 mb-2">Turbines, Rigid Foundation</div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between"><span class="text-yellow-600">Warning:</span> <span>7.1 mm/s</span></div>
                                        <div class="flex justify-between"><span class="text-red-600">Critical:</span> <span>18.0 mm/s</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div style="border-radius: 16px !important;" class="bg-white shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Pengaturan Notifikasi</h3>
                                    <p class="text-[12px] text-gray-500 font-medium">Konfigurasi alert dan notifikasi sistem</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full border border-blue-100">CONFIG</span>
                        </div>

                        <form id="notificationForm" onsubmit="saveNotifications(event)">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Toggle Options -->
                                <div class="space-y-4">
                                    <!-- Email Notifications -->
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl border border-gray-100 hover:shadow-sm transition-all">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100">
                                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-800">Notifikasi Email</h4>
                                                <p class="text-xs text-gray-400">Kirim alert ke email terdaftar</p>
                                            </div>
                                        </div>
                                        <button type="button" id="emailToggle" onclick="toggleSwitch('email')"
                                            class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 shadow-inner"
                                            style="background-color: {{ $notificationConfig['email_enabled'] ? '#059669' : '#d1d5db' }}">
                                            <span id="emailKnob" class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-all duration-300"
                                                style="transform: translateX({{ $notificationConfig['email_enabled'] ? '24px' : '4px' }})"></span>
                                        </button>
                                        <input type="hidden" id="emailEnabled" value="{{ $notificationConfig['email_enabled'] ? '1' : '0' }}">
                                    </div>

                                    <!-- Sound Notifications -->
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl border border-gray-100 hover:shadow-sm transition-all">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100">
                                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-800">Notifikasi Suara</h4>
                                                <p class="text-xs text-gray-400">Bunyi alarm saat alert muncul</p>
                                            </div>
                                        </div>
                                        <button type="button" id="soundToggle" onclick="toggleSwitch('sound')"
                                            class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 shadow-inner"
                                            style="background-color: {{ $notificationConfig['alert_sound_enabled'] ? '#059669' : '#d1d5db' }}">
                                            <span id="soundKnob" class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-all duration-300"
                                                style="transform: translateX({{ $notificationConfig['alert_sound_enabled'] ? '24px' : '4px' }})"></span>
                                        </button>
                                        <input type="hidden" id="soundEnabled" value="{{ $notificationConfig['alert_sound_enabled'] ? '1' : '0' }}">
                                    </div>

                                    <!-- Auto Acknowledge -->
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl border border-gray-100 hover:shadow-sm transition-all">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100">
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-800">Auto-Acknowledge</h4>
                                                <p class="text-xs text-gray-400">Otomatis acknowledge setelah waktu tertentu</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="number" id="autoAcknowledgeHours" value="{{ $notificationConfig['auto_acknowledge_hours'] }}" min="1" max="168"
                                                class="w-16 rounded-lg border-gray-200 text-sm font-semibold text-center focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">jam</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Recipients -->
                                <div id="emailRecipientsContainer" class="{{ $notificationConfig['email_enabled'] ? '' : 'hidden' }}">
                                    <div class="bg-gradient-to-br from-blue-50/50 to-indigo-50/50 rounded-xl p-5 border border-blue-100/50 h-full">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <label class="text-sm font-bold text-gray-700">Daftar Penerima Email</label>
                                        </div>
                                        <textarea id="emailRecipients" rows="5" placeholder="contoh@email.com&#10;admin@perusahaan.com&#10;teknisi@domain.com"
                                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm placeholder-gray-300 resize-none">{{ $notificationConfig['email_recipients'] }}</textarea>
                                        <div class="flex items-center space-x-1 mt-2">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-[10px] text-gray-400 font-medium">Masukkan satu alamat email per baris</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <p class="text-xs text-gray-400">Perubahan akan berlaku segera setelah disimpan</p>
                                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm hover:shadow-md flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Simpan Pengaturan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Machine Threshold Edit Modal -->
            <div id="thresholdModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeThresholdModal()"></div>
                    <div class="relative bg-white rounded-xl shadow-xl sm:max-w-lg sm:w-full">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900" id="modalMachineName">Edit Threshold</h3>
                                <button onclick="closeThresholdModal()" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form id="machineThresholdForm" onsubmit="saveMachineThreshold(event)">
                            <input type="hidden" id="modalMachineId">
                            <div class="px-6 py-4 space-y-4">
                                <!-- ISO Class Preset -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preset ISO 10816-3</label>
                                    <select id="modalIsoClass" onchange="applyIsoPreset()"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="Class I">Class I - Motor ≤ 15 kW (1.8 / 4.5)</option>
                                        <option value="Class II">Class II - Motor 15-75 kW (2.8 / 7.1)</option>
                                        <option value="Class III">Class III - Motor 75-300 kW (4.5 / 11.2)</option>
                                        <option value="Class IV">Class IV - Turbines (7.1 / 18.0)</option>
                                    </select>
                                </div>

                                <!-- Motor Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Daya Motor (HP)</label>
                                        <input type="number" step="0.1" id="modalMotorPower" placeholder="e.g. 20"
                                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RPM Motor</label>
                                        <input type="number" id="modalMotorRpm" placeholder="e.g. 3500"
                                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                </div>

                                <!-- Thresholds -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                                            <span class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></span>
                                            Warning (mm/s)
                                        </label>
                                        <input type="number" step="0.1" id="modalWarning" required
                                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                                            <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                            Critical (mm/s)
                                        </label>
                                        <input type="number" step="0.1" id="modalCritical" required
                                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                </div>

                                <!-- Info Note -->
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs text-blue-700">
                                        <strong>Tip:</strong> Pilih preset ISO sesuai daya motor. Untuk Westfalia CA 505-01-12:
                                        <br>• Motor Scroll (20 HP) → Class I
                                        <br>• Motor Bowl (120 HP) → Class II
                                    </p>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
                                <button type="button" onclick="closeThresholdModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                                    Simpan Threshold
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Detail Modal -->
    <div id="alertDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Alert</h3>
                    <button onclick="closeAlertModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6" id="alertDetailContent">
                <!-- Alert details will be loaded here -->
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex space-x-3">
                <button onclick="acknowledgeFromModal()" id="modalAckBtn" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                    Acknowledge
                </button>
                <button onclick="resolveFromModal()" id="modalResolveBtn" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Resolve
                </button>
                <button onclick="closeAlertModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Global variables
        let currentAlertId = null;
        let alertsData = [];
        let currentPage = 1;
        let historyPage = 1;
        let alertsByMachineChart = null;
        let alertsBySeverityChart = null;

        // Tab switching
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Remove active state from all tabs
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/50');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/50');
            activeTab.classList.remove('border-transparent', 'text-gray-500');

            // Load data for specific tabs
            if (tabName === 'alerts') loadAlerts();
            if (tabName === 'history') loadHistory();
            if (tabName === 'overview') loadStats();
            if (tabName === 'settings') loadMachineThresholds();
        }

        // Load statistics
        async function loadStats() {
            try {
                const response = await fetch('/api/alert-management/stats');
                const data = await response.json();

                if (data.success) {
                    const stats = data.stats;
                    document.getElementById('statToday').textContent = stats.today;
                    document.getElementById('statCritical').textContent = stats.by_severity.critical;
                    document.getElementById('statWarning').textContent = stats.by_severity.warning;
                    document.getElementById('statAcknowledged').textContent = stats.acknowledged;
                    document.getElementById('statUnacknowledged').textContent = stats.unacknowledged;

                    // Update header indicator
                    document.getElementById('activeAlertCount').textContent = stats.last_24h + ' Alert Aktif';

                    // Update charts
                    updateCharts(stats);

                    // Load recent alerts
                    loadRecentAlerts();
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Update charts
        function updateCharts(stats) {
            // Alerts by Machine Chart
            const machineCtx = document.getElementById('alertsByMachineChart');
            if (alertsByMachineChart) alertsByMachineChart.destroy();

            alertsByMachineChart = new Chart(machineCtx, {
                type: 'bar',
                data: {
                    labels: stats.by_machine.map(m => m.name),
                    datasets: [{
                        label: 'Jumlah Alert',
                        data: stats.by_machine.map(m => m.alert_count),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    resizeDelay: 0,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Alerts by Severity Chart
            const severityCtx = document.getElementById('alertsBySeverityChart');
            if (alertsBySeverityChart) alertsBySeverityChart.destroy();

            alertsBySeverityChart = new Chart(severityCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Bahaya', 'Peringatan'],
                    datasets: [{
                        data: [stats.by_severity.critical, stats.by_severity.warning],
                        backgroundColor: ['#ef4444', '#eab308'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    resizeDelay: 0,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Load recent alerts for overview
        async function loadRecentAlerts() {
            try {
                const response = await fetch('/api/alert-management/alerts?per_page=5');
                const data = await response.json();

                if (data.success && data.data.data.length > 0) {
                    const container = document.getElementById('recentAlertsList');
                    container.innerHTML = data.data.data.map(alert => `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="showAlertDetail(${alert.id})">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 rounded-lg ${getSeverityBgClass(alert.severity)}">
                                    ${getSeverityIcon(alert.severity)}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${alert.machine_name}</p>
                                    <p class="text-xs text-gray-500">RMS: ${alert.rms} mm/s • ${alert.time_ago}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${getSeverityClass(alert.severity)}">
                                ${alert.severity_label}
                            </span>
                        </div>
                    `).join('');
                } else {
                    document.getElementById('recentAlertsList').innerHTML = `
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Tidak ada alert aktif</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading recent alerts:', error);
            }
        }

        // Load alerts list
        async function loadAlerts(page = 1) {
            currentPage = page;
            const params = new URLSearchParams({
                page: page,
                per_page: 15,
                machine_id: document.getElementById('filterMachine').value,
                severity: document.getElementById('filterSeverity').value,
                date_from: document.getElementById('filterDateFrom').value,
                date_to: document.getElementById('filterDateTo').value,
            });

            try {
                const response = await fetch('/api/alert-management/alerts?' + params);
                const data = await response.json();

                if (data.success) {
                    alertsData = data.data.data;
                    renderAlertsTable(alertsData);
                    renderPagination(data.data);
                }
            } catch (error) {
                console.error('Error loading alerts:', error);
            }
        }

        // Render alerts table
        function renderAlertsTable(alerts) {
            const tbody = document.getElementById('alertsTableBody');

            if (alerts.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada alert ditemukan
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = alerts.map(alert => `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="alert-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" value="${alert.id}">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">#${alert.id}</td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">${alert.machine_name}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">${alert.location}</td>
                    <td class="px-4 py-3 text-sm font-mono font-medium text-gray-900">${alert.rms}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getSeverityClass(alert.severity)}">
                            ${alert.severity_label}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">${alert.time_ago}</td>
                    <td class="px-4 py-3">
                        ${alert.acknowledged
                            ? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Acknowledged</span>'
                            : '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Pending</span>'
                        }
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <button onclick="showAlertDetail(${alert.id})" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            ${!alert.acknowledged ? `
                                <button onclick="acknowledgeAlert(${alert.id})" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Render pagination
        function renderPagination(paginationData) {
            document.getElementById('paginationInfo').textContent =
                `Menampilkan ${paginationData.from || 0} - ${paginationData.to || 0} dari ${paginationData.total} alert`;

            const controls = document.getElementById('paginationControls');
            let html = '';

            if (paginationData.prev_page_url) {
                html += `<button onclick="loadAlerts(${paginationData.current_page - 1})" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Prev</button>`;
            }

            html += `<span class="px-3 py-1 text-sm bg-emerald-100 text-emerald-700 rounded-lg">${paginationData.current_page}</span>`;

            if (paginationData.next_page_url) {
                html += `<button onclick="loadAlerts(${paginationData.current_page + 1})" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Next</button>`;
            }

            controls.innerHTML = html;
        }

        // Load history
        async function loadHistory(page = 1) {
            historyPage = page;
            const params = new URLSearchParams({
                page: page,
                per_page: 20,
                machine_id: document.getElementById('historyFilterMachine').value,
                date_from: document.getElementById('historyDateFrom').value,
                date_to: document.getElementById('historyDateTo').value,
            });

            try {
                const response = await fetch('/api/alert-management/history?' + params);
                const data = await response.json();

                if (data.success) {
                    renderHistoryTable(data.data.data);
                    renderHistoryPagination(data.data);
                }
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // Render history table
        function renderHistoryTable(history) {
            const tbody = document.getElementById('historyTableBody');

            if (history.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada riwayat ditemukan
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = history.map(item => `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">#${item.id}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${item.machine_name}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${item.location}</td>
                    <td class="px-4 py-3 text-sm font-mono font-medium text-gray-900">${item.rms}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getSeverityClass(item.severity)}">
                            ${item.severity_label}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">${item.timestamp}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${item.acknowledged_by || '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${item.acknowledged_at || '-'}</td>
                </tr>
            `).join('');
        }

        // Render history pagination
        function renderHistoryPagination(paginationData) {
            document.getElementById('historyPaginationInfo').textContent =
                `Menampilkan ${paginationData.from || 0} - ${paginationData.to || 0} dari ${paginationData.total} riwayat`;

            const controls = document.getElementById('historyPaginationControls');
            let html = '';

            if (paginationData.prev_page_url) {
                html += `<button onclick="loadHistory(${paginationData.current_page - 1})" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Prev</button>`;
            }

            html += `<span class="px-3 py-1 text-sm bg-emerald-100 text-emerald-700 rounded-lg">${paginationData.current_page}</span>`;

            if (paginationData.next_page_url) {
                html += `<button onclick="loadHistory(${paginationData.current_page + 1})" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Next</button>`;
            }

            controls.innerHTML = html;
        }

        // Show alert detail modal
        async function showAlertDetail(alertId) {
            currentAlertId = alertId;
            const alert = alertsData.find(a => a.id === alertId);

            if (!alert) return;

            const content = document.getElementById('alertDetailContent');
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-500">Severity</span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full ${getSeverityClass(alert.severity)}">
                            ${alert.severity_label}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Mesin</p>
                            <p class="text-sm font-medium text-gray-900">${alert.machine_name}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Lokasi</p>
                            <p class="text-sm font-medium text-gray-900">${alert.location}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">RMS Velocity</p>
                            <p class="text-sm font-medium text-gray-900">${alert.rms} mm/s</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Peak Amplitude</p>
                            <p class="text-sm font-medium text-gray-900">${alert.peak_amp} mm/s</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Dominant Frequency</p>
                            <p class="text-sm font-medium text-gray-900">${alert.dominant_freq_hz} Hz</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Waktu Terjadi</p>
                            <p class="text-sm font-medium text-gray-900">${alert.timestamp}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Catatan</p>
                        <textarea id="alertNotes" rows="2" class="w-full mt-2 rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Tambahkan catatan...">${alert.notes || ''}</textarea>
                    </div>
                </div>
            `;

            // Update modal buttons based on status
            document.getElementById('modalAckBtn').style.display = alert.acknowledged ? 'none' : 'block';
            document.getElementById('modalResolveBtn').style.display = alert.resolved ? 'none' : 'block';

            document.getElementById('alertDetailModal').classList.remove('hidden');
        }

        // Close modal
        function closeAlertModal() {
            document.getElementById('alertDetailModal').classList.add('hidden');
            currentAlertId = null;
        }

        // Acknowledge alert
        async function acknowledgeAlert(alertId) {
            try {
                const response = await fetch(`/api/alert-management/alerts/${alertId}/acknowledge`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    showToast('Alert berhasil di-acknowledge', 'success');
                    loadAlerts(currentPage);
                    loadStats();
                }
            } catch (error) {
                console.error('Error acknowledging alert:', error);
                showToast('Gagal acknowledge alert', 'error');
            }
        }

        // Acknowledge from modal
        async function acknowledgeFromModal() {
            if (!currentAlertId) return;

            const notes = document.getElementById('alertNotes')?.value || '';

            try {
                const response = await fetch(`/api/alert-management/alerts/${currentAlertId}/acknowledge`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ notes })
                });

                const data = await response.json();
                if (data.success) {
                    showToast('Alert berhasil di-acknowledge', 'success');
                    closeAlertModal();
                    loadAlerts(currentPage);
                    loadStats();
                }
            } catch (error) {
                console.error('Error acknowledging alert:', error);
                showToast('Gagal acknowledge alert', 'error');
            }
        }

        // Resolve from modal
        async function resolveFromModal() {
            if (!currentAlertId) return;

            const notes = document.getElementById('alertNotes')?.value || '';

            try {
                const response = await fetch(`/api/alert-management/alerts/${currentAlertId}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ resolution_notes: notes })
                });

                const data = await response.json();
                if (data.success) {
                    showToast('Alert berhasil di-resolve', 'success');
                    closeAlertModal();
                    loadAlerts(currentPage);
                    loadStats();
                }
            } catch (error) {
                console.error('Error resolving alert:', error);
                showToast('Gagal resolve alert', 'error');
            }
        }

        // Bulk acknowledge
        async function bulkAcknowledge() {
            const checkboxes = document.querySelectorAll('.alert-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => parseInt(cb.value));

            if (ids.length === 0) {
                showToast('Pilih alert terlebih dahulu', 'warning');
                return;
            }

            try {
                const response = await fetch('/api/alert-management/alerts/bulk-acknowledge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids })
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    loadAlerts(currentPage);
                    loadStats();
                }
            } catch (error) {
                console.error('Error bulk acknowledging:', error);
                showToast('Gagal acknowledge alerts', 'error');
            }
        }

        // Toggle select all
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll').checked;
            document.querySelectorAll('.alert-checkbox').forEach(cb => cb.checked = selectAll);
        }

        // ISO Threshold presets
        const isoPresets = {
            'Class I': { warning: 1.8, critical: 4.5 },
            'Class II': { warning: 2.8, critical: 7.1 },
            'Class III': { warning: 4.5, critical: 11.2 },
            'Class IV': { warning: 7.1, critical: 18.0 }
        };

        // Load machine thresholds
        async function loadMachineThresholds() {
            try {
                const response = await fetch('/api/alert-management/machine-thresholds');
                const data = await response.json();

                if (data.success) {
                    renderMachineThresholdTable(data.machines);
                }
            } catch (error) {
                console.error('Error loading machine thresholds:', error);
                showToast('Gagal memuat data threshold mesin', 'error');
            }
        }

        // Render machine threshold table
        function renderMachineThresholdTable(machines) {
            const tbody = document.getElementById('machineThresholdTable');

            if (machines.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Belum ada mesin terdaftar
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = machines.map(machine => `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">${machine.name}</div>
                    </td>
                    <td class="py-3 px-4 text-gray-600">${machine.location || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        ${machine.motor_power_hp ? machine.motor_power_hp + ' HP' : '-'}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getIsoClassColor(machine.iso_class)}">
                            ${machine.iso_class || 'Class I'}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="font-mono text-yellow-600 font-semibold">${machine.threshold_warning}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="font-mono text-red-600 font-semibold">${machine.threshold_critical}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button onclick="openThresholdModal(${machine.id}, '${machine.name}', ${machine.threshold_warning}, ${machine.threshold_critical}, '${machine.iso_class || 'Class I'}', ${machine.motor_power_hp || 'null'}, ${machine.motor_rpm || 'null'})"
                            class="px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Get ISO class badge color
        function getIsoClassColor(isoClass) {
            const colors = {
                'Class I': 'bg-blue-100 text-blue-700',
                'Class II': 'bg-green-100 text-green-700',
                'Class III': 'bg-yellow-100 text-yellow-700',
                'Class IV': 'bg-red-100 text-red-700'
            };
            return colors[isoClass] || colors['Class I'];
        }

        // Open threshold modal
        function openThresholdModal(machineId, machineName, warning, critical, isoClass, motorPower, motorRpm) {
            document.getElementById('modalMachineId').value = machineId;
            document.getElementById('modalMachineName').textContent = 'Edit Threshold - ' + machineName;
            document.getElementById('modalWarning').value = warning;
            document.getElementById('modalCritical').value = critical;
            document.getElementById('modalIsoClass').value = isoClass || 'Class I';
            document.getElementById('modalMotorPower').value = motorPower || '';
            document.getElementById('modalMotorRpm').value = motorRpm || '';

            document.getElementById('thresholdModal').classList.remove('hidden');
        }

        // Close threshold modal
        function closeThresholdModal() {
            document.getElementById('thresholdModal').classList.add('hidden');
        }

        // Apply ISO preset when dropdown changes
        function applyIsoPreset() {
            const isoClass = document.getElementById('modalIsoClass').value;
            const preset = isoPresets[isoClass];

            if (preset) {
                document.getElementById('modalWarning').value = preset.warning;
                document.getElementById('modalCritical').value = preset.critical;
            }
        }

        // Save machine threshold
        async function saveMachineThreshold(e) {
            e.preventDefault();

            const data = {
                machine_id: parseInt(document.getElementById('modalMachineId').value),
                warning: parseFloat(document.getElementById('modalWarning').value),
                critical: parseFloat(document.getElementById('modalCritical').value),
                iso_class: document.getElementById('modalIsoClass').value,
                motor_power_hp: document.getElementById('modalMotorPower').value || null,
                motor_rpm: document.getElementById('modalMotorRpm').value || null
            };

            // Validate thresholds
            if (data.warning >= data.critical) {
                showToast('Threshold Warning harus lebih kecil dari Critical', 'error');
                return;
            }

            try {
                const response = await fetch('/api/alert-management/thresholds', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    showToast('Threshold berhasil disimpan untuk ' + result.config.machine_name, 'success');
                    closeThresholdModal();
                    loadMachineThresholds();
                    loadStats(); // Refresh stats
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error saving threshold:', error);
                showToast('Gagal menyimpan threshold', 'error');
            }
        }

        // Toggle switch function
        function toggleSwitch(type) {
            console.log('toggleSwitch called with type:', type);

            const toggle = document.getElementById(type + 'Toggle');
            const knob = document.getElementById(type + 'Knob');
            const input = document.getElementById(type + 'Enabled');

            console.log('Elements found:', { toggle, knob, input });
            console.log('Current input value:', input ? input.value : 'not found');

            if (!toggle || !knob || !input) {
                console.error('Missing elements for toggle:', type);
                return;
            }

            const isEnabled = input.value === '1';
            console.log('isEnabled:', isEnabled);

            if (isEnabled) {
                // Turn off
                console.log('Turning OFF');
                toggle.style.backgroundColor = '#d1d5db'; // gray-300
                knob.style.transform = 'translateX(4px)';
                input.value = '0';
            } else {
                // Turn on
                console.log('Turning ON');
                toggle.style.backgroundColor = '#059669'; // emerald-600
                knob.style.transform = 'translateX(24px)';
                input.value = '1';
            }

            console.log('New input value:', input.value);

            // Toggle email recipients visibility
            if (type === 'email') {
                const container = document.getElementById('emailRecipientsContainer');
                if (container) {
                    container.classList.toggle('hidden', input.value === '0');
                }
            }
        }

        // Save notifications
        async function saveNotifications(e) {
            e.preventDefault();

            const data = {
                email_enabled: document.getElementById('emailEnabled').value === '1',
                email_recipients: document.getElementById('emailRecipients').value,
                alert_sound_enabled: document.getElementById('soundEnabled').value === '1',
                auto_acknowledge_hours: parseInt(document.getElementById('autoAcknowledgeHours').value),
            };

            try {
                const response = await fetch('/api/alert-management/notifications', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    showToast('Pengaturan notifikasi berhasil disimpan', 'success');
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error saving notifications:', error);
                showToast('Gagal menyimpan pengaturan', 'error');
            }
        }

        // Export alerts
        function exportAlerts() {
            const params = new URLSearchParams({
                machine_id: document.getElementById('filterMachine')?.value || '',
                date_from: document.getElementById('filterDateFrom')?.value || '',
                date_to: document.getElementById('filterDateTo')?.value || '',
            });
            window.location.href = '/api/alert-management/export?' + params;
        }

        // Helper functions
        function getSeverityClass(severity) {
            const classes = {
                'critical': 'bg-red-100 text-red-800',
                'warning': 'bg-yellow-100 text-yellow-800',
                'normal': 'bg-green-100 text-green-800'
            };
            return classes[severity] || classes['normal'];
        }

        function getSeverityBgClass(severity) {
            const classes = {
                'critical': 'bg-red-100',
                'warning': 'bg-yellow-100',
                'normal': 'bg-green-100'
            };
            return classes[severity] || classes['normal'];
        }

        function getSeverityIcon(severity) {
            if (severity === 'critical') {
                return `<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>`;
            }
            return `<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-opacity duration-300 ${
                type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' :
                type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Update current time
        function updateTime() {
            const now = new Date();
            const formatted = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentTime').textContent = formatted;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            setInterval(updateTime, 1000);
            setInterval(loadStats, 30000); // Refresh stats every 30 seconds
        });
    </script>
    @endpush
</x-app-layout>
