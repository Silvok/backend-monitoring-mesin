<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">
                    Real-Time Sensor Monitoring
                </h2>
                <!-- Live Status Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Live</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Machine Selector -->
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <label for="machineSelector" class="block text-sm font-bold text-gray-900 mb-3">
                        🔧 Select Machine to Monitor
                    </label>
                    <select id="machineSelector" class="w-full md:w-1/2 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 font-medium">
                        <option value="">-- Select a Machine --</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}"
                                data-status="{{ $machine->latestAnalysis?->condition_status ?? 'UNKNOWN' }}"
                                data-location="{{ $machine->location }}">
                                {{ $machine->name }} ({{ $machine->location }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Machine Status Card -->
            <div id="machineStatusCard" class="mb-6 hidden">
                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
                    <!-- Status Bar -->
                    <div id="statusBar" class="h-2 bg-gray-400"></div>

                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 id="machineName" class="text-2xl font-bold text-gray-900 mb-2">-</h3>
                                <p id="machineLocation" class="text-sm text-gray-600">Location: -</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span id="statusBadge" class="px-4 py-2 rounded-full text-sm font-bold bg-gray-200 text-gray-700">
                                    UNKNOWN
                                </span>
                                <div id="statusIcon" class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4 border border-emerald-200">
                                <p class="text-xs font-semibold text-emerald-700 mb-1">RMS VALUE</p>
                                <p id="rmsValue" class="text-2xl font-bold text-gray-900">0.0000</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                <p class="text-xs font-semibold text-blue-700 mb-1">PEAK AMPLITUDE</p>
                                <p id="peakValue" class="text-2xl font-bold text-gray-900">0.0000</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                                <p class="text-xs font-semibold text-purple-700 mb-1">FREQUENCY</p>
                                <p id="freqValue" class="text-2xl font-bold text-gray-900">0 Hz</p>
                            </div>
                        </div>

                        <!-- Last Check -->
                        <div class="text-sm text-gray-600">
                            <span class="font-semibold">Last Check:</span>
                            <span id="lastCheck">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div id="quickStatsSection" class="mb-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Total Readings Today -->
                    <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">READINGS TODAY</p>
                                <p id="statReadings" class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Percentage -->
                    <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">UPTIME TODAY</p>
                                <p id="statUptime" class="text-2xl font-bold text-gray-900">--%</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Last Anomaly -->
                    <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">LAST ANOMALY</p>
                                <p id="statLastAnomaly" class="text-sm font-bold text-gray-900">Never</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Time in Normal State -->
                    <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-emerald-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">NORMAL STATE</p>
                                <p id="statNormalTime" class="text-2xl font-bold text-gray-900">--%</p>
                            </div>
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Sensor Values -->
            <div id="sensorValuesSection" class="mb-6 hidden">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-blue-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Real-Time Sensor Values</h3>
                            <div class="flex items-center space-x-2 px-3 py-1 bg-white rounded-full border border-emerald-300 shadow-sm">
                                <div class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </div>
                                <span class="text-xs font-semibold text-emerald-700">Updating</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Acceleration 3-Axis Display -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Acceleration (G-Force)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- AX Card -->
                                <div class="relative">
                                    <div id="axCard" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-300 transition-all duration-300">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <p class="text-xs font-bold text-gray-600 mb-1">AXIS X</p>
                                                <p id="axValue" class="text-4xl font-bold text-gray-900">0.0000</p>
                                                <p class="text-xs text-gray-500 mt-1">G</p>
                                            </div>
                                            <div id="axIcon" class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="relative pt-1">
                                            <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200">
                                                <div id="axBar" class="w-0 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-400 transition-all duration-500"></div>
                                            </div>
                                        </div>
                                        <p id="axStatus" class="text-xs font-semibold text-gray-600 mt-2 text-center">NORMAL</p>
                                    </div>
                                </div>

                                <!-- AY Card -->
                                <div class="relative">
                                    <div id="ayCard" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-300 transition-all duration-300">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <p class="text-xs font-bold text-gray-600 mb-1">AXIS Y</p>
                                                <p id="ayValue" class="text-4xl font-bold text-gray-900">0.0000</p>
                                                <p class="text-xs text-gray-500 mt-1">G</p>
                                            </div>
                                            <div id="ayIcon" class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="relative pt-1">
                                            <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200">
                                                <div id="ayBar" class="w-0 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-400 transition-all duration-500"></div>
                                            </div>
                                        </div>
                                        <p id="ayStatus" class="text-xs font-semibold text-gray-600 mt-2 text-center">NORMAL</p>
                                    </div>
                                </div>

                                <!-- AZ Card -->
                                <div class="relative">
                                    <div id="azCard" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-300 transition-all duration-300">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <p class="text-xs font-bold text-gray-600 mb-1">AXIS Z</p>
                                                <p id="azValue" class="text-4xl font-bold text-gray-900">0.0000</p>
                                                <p class="text-xs text-gray-500 mt-1">G</p>
                                            </div>
                                            <div id="azIcon" class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="relative pt-1">
                                            <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200">
                                                <div id="azBar" class="w-0 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-400 transition-all duration-500"></div>
                                            </div>
                                        </div>
                                        <p id="azStatus" class="text-xs font-semibold text-gray-600 mt-2 text-center">NORMAL</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Temperature Display -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Temperature</h4>
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border-2 border-orange-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div id="tempIcon" class="w-16 h-16 rounded-xl bg-orange-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-600 mb-1">SENSOR TEMPERATURE</p>
                                            <p id="tempValue" class="text-5xl font-bold text-gray-900">--</p>
                                            <p class="text-sm text-gray-500 mt-1">°C</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p id="tempStatus" class="text-sm font-bold text-gray-600 px-4 py-2 bg-white rounded-lg border-2 border-gray-300">
                                            NORMAL
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">Range: 0-80°C</p>
                                    </div>
                                </div>
                                <div class="relative pt-4 mt-4 border-t border-orange-200">
                                    <div class="overflow-hidden h-3 text-xs flex rounded-full bg-white">
                                        <div id="tempBar" class="w-0 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-orange-400 transition-all duration-500"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs font-bold text-gray-700 mb-2">Status Indicators:</p>
                            <div class="flex flex-wrap gap-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                    <span class="text-xs text-gray-600">Normal (0-0.5G / 0-60°C)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                    <span class="text-xs text-gray-600">Warning (0.5-1.0G / 60-80°C)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                    <span class="text-xs text-gray-600">Critical (>1.0G / >80°C)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Statistik & Alert Panel (2 columns) -->
            <div id="statsAlertsSection" class="mb-6 hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Panel Statistik -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                            <h3 class="text-lg font-bold text-gray-900">📊 Statistical Analysis</h3>
                        </div>
                        <div class="p-6">
                            <!-- Axis X Stats -->
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <p class="text-xs font-bold text-blue-600 mb-2">AXIS X</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Min</p>
                                        <p id="statAxMin" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Max</p>
                                        <p id="statAxMax" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Avg</p>
                                        <p id="statAxAvg" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500">Peak</p>
                                    <p id="statAxPeak" class="text-xs font-semibold text-red-600">No peaks detected</p>
                                </div>
                            </div>

                            <!-- Axis Y Stats -->
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <p class="text-xs font-bold text-green-600 mb-2">AXIS Y</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Min</p>
                                        <p id="statAyMin" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Max</p>
                                        <p id="statAyMax" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Avg</p>
                                        <p id="statAyAvg" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500">Peak</p>
                                    <p id="statAyPeak" class="text-xs font-semibold text-red-600">No peaks detected</p>
                                </div>
                            </div>

                            <!-- Axis Z Stats -->
                            <div>
                                <p class="text-xs font-bold text-purple-600 mb-2">AXIS Z</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Min</p>
                                        <p id="statAzMin" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Max</p>
                                        <p id="statAzMax" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Avg</p>
                                        <p id="statAzAvg" class="text-sm font-bold text-gray-900">0.0000 G</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500">Peak</p>
                                    <p id="statAzPeak" class="text-xs font-semibold text-red-600">No peaks detected</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Panel -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">🚨 Active Alerts</h3>
                            <span id="alertCount" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">0</span>
                        </div>
                        <div class="p-6">
                            <div id="alertList" class="space-y-3 max-h-80 overflow-y-auto">
                                <p class="text-sm text-gray-500 text-center py-8">No active alerts</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Multi-axis Chart -->
            <div id="chartSection" class="mb-6 hidden">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">📈 Real-Time Multi-Axis Chart</h3>
                                <p class="text-xs text-gray-600 mt-1">Live acceleration monitoring across all axes</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-wrap gap-2">
                                <!-- Export Data Button -->
                                <button id="exportDataBtn" class="px-4 py-2 text-xs font-semibold rounded-full bg-gradient-to-r from-teal-500 to-cyan-500 text-white shadow-md hover:shadow-lg transition transform hover:scale-105 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    <span>Export CSV</span>
                                </button>

                                <!-- Threshold Config Button -->
                                <button id="thresholdConfigBtn" class="px-4 py-2 text-xs font-semibold rounded-full bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md hover:shadow-lg transition transform hover:scale-105 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    </svg>
                                    <span>Thresholds</span>
                                </button>

                                <!-- Chart Mode Toggle -->
                                <div class="flex items-center bg-gray-100 rounded-full shadow-sm p-1 gap-1">
                                    <button id="liveModeBtn" class="px-4 py-2 text-xs font-semibold rounded-full bg-emerald-500 text-white shadow-md transition transform hover:scale-105">
                                        Live
                                    </button>
                                    <button id="historicalModeBtn" class="px-4 py-2 text-xs font-semibold rounded-full bg-white text-gray-900 border border-gray-300 hover:border-purple-400 hover:text-purple-600 transition">
                                        Historical
                                    </button>
                                </div>

                                <!-- Date Selector (Hidden by default) -->
                                <div id="dateSelector" class="hidden">
                                    <input type="date"
                                        id="chartDatePicker"
                                        class="px-4 py-2 rounded-full border border-slate-200 bg-white shadow-sm text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-purple-400 focus:border-purple-300"
                                        value="{{ now()->format('Y-m-d') }}"
                                        max="{{ now()->format('Y-m-d') }}">
                                </div>

                                <!-- Time Range Selector (for historical mode) -->
                                <div id="timeRangeSelector" class="hidden">
                                    <select id="historicalTimeRange" class="px-4 py-2 rounded-full border border-slate-200 bg-white shadow-sm text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-purple-400 focus:border-purple-300">
                                        <option value="1">Last 1 Hour</option>
                                        <option value="3">Last 3 Hours</option>
                                        <option value="6">Last 6 Hours</option>
                                        <option value="24" selected>Full Day (24h)</option>
                                    </select>
                                </div>

                                <!-- Time Window Selector (for live mode) -->
                                <div id="liveTimeWindow">
                                    <select id="timeWindowSelector" class="px-4 py-2 rounded-full border border-slate-200 bg-white shadow-sm text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-sky-400 focus:border-sky-300">
                                        <option value="60">Last 1 Minute</option>
                                        <option value="300" selected>Last 5 Minutes</option>
                                    </select>
                                </div>

                                <!-- Live Indicator -->
                                <div id="liveIndicator" class="flex items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-sky-50 to-emerald-50 rounded-full border border-sky-200 shadow-sm">
                                    <div class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </div>
                                    <span class="text-xs font-semibold text-sky-700">Live Update</span>
                                </div>

                                <!-- Historical Indicator -->
                                <div id="historicalIndicator" class="hidden flex items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-purple-50 to-pink-50 rounded-full border border-purple-200 shadow-sm">
                                    <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-purple-700">Historical Data</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative" style="height: 400px;">
                            <canvas id="multiAxisChart"></canvas>
                        </div>
                        <!-- Chart Legend -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap gap-4 justify-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-1 bg-blue-500"></div>
                                    <span class="text-xs text-gray-600 font-medium">Axis X</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-1 bg-green-500"></div>
                                    <span class="text-xs text-gray-600 font-medium">Axis Y</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-1 bg-purple-500"></div>
                                    <span class="text-xs text-gray-600 font-medium">Axis Z</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-1 bg-yellow-500 border-t-2 border-dashed border-yellow-600"></div>
                                    <span class="text-xs text-gray-600 font-medium">Warning Threshold (±0.5G)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-1 bg-red-500 border-t-2 border-dashed border-red-600"></div>
                                    <span class="text-xs text-gray-600 font-medium">Critical Threshold (±1.0G)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sensor Data Table -->
            <div id="liveFeedSection" class="mb-6 hidden">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Live Data Feed</h3>
                        <p class="text-xs text-gray-500">Menampilkan 10-20 data terbaru secara otomatis</p>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="max-h-64 overflow-y-auto" id="liveFeedContainer">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timestamp</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Accel X</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Accel Y</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Accel Z</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Suhu (°C)</th>
                                    </tr>
                                </thead>
                                <tbody id="liveFeedBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Live data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Threshold Configuration Modal -->
    <div id="thresholdModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">⚙️ Threshold Configuration</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <!-- Warning Threshold -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">Warning Threshold (G)</label>
                    <input type="range" id="warningThreshold" min="0.1" max="2.0" step="0.1" value="0.5" class="w-full">
                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                        <span>0.1G</span>
                        <span id="warningValue" class="font-bold text-yellow-600">0.5G</span>
                        <span>2.0G</span>
                    </div>
                </div>

                <!-- Critical Threshold -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">Critical Threshold (G)</label>
                    <input type="range" id="criticalThreshold" min="0.5" max="3.0" step="0.1" value="1.0" class="w-full">
                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                        <span>0.5G</span>
                        <span id="criticalValue" class="font-bold text-red-600">1.0G</span>
                        <span>3.0G</span>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button id="applyThresholdBtn" class="flex-1 px-4 py-2 bg-emerald-500 text-white font-semibold rounded-lg hover:bg-emerald-600 transition">
                        Apply
                    </button>
                    <button id="cancelThresholdBtn" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMachineId = null;
        let updateInterval = null;
        let wsConnection = null;
        let multiAxisChart = null;
        let chartUpdateInterval = null;
        let timeWindow = 300; // seconds (5 minutes default)
        let chartMode = 'live'; // 'live' or 'historical'
        let latestSampleTimestamp = null; // to auto-pick date for historical

        // Chart data storage
        const chartData = {
            labels: [],
            ax: [],
            ay: [],
            az: []
        };

        // Statistics storage
        const statistics = {
            ax: { min: Infinity, max: -Infinity, sum: 0, count: 0, peak: null, values: [] },
            ay: { min: Infinity, max: -Infinity, sum: 0, count: 0, peak: null, values: [] },
            az: { min: Infinity, max: -Infinity, sum: 0, count: 0, peak: null, values: [] },
            totalReadings: 0,
            normalCount: 0,
            anomalyCount: 0,
            lastAnomaly: null
        };

        // Thresholds
        const THRESHOLDS = {
            acceleration: {
                normal: 0.5,
                warning: 1.0
            },
            temperature: {
                normal: 60,
                warning: 80
            }
        };

        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');

            // Start clock
            updateClock();
            setInterval(updateClock, 1000);

        // Chart Mode Toggle
        document.getElementById('liveModeBtn').addEventListener('click', function() {
            switchChartMode('live');
        });

        document.getElementById('historicalModeBtn').addEventListener('click', function() {
            switchChartMode('historical');
        });

        function switchChartMode(mode) {
            chartMode = mode;

            if (mode === 'live') {
                // Show live controls
                document.getElementById('liveModeBtn').className = 'px-4 py-2 text-xs font-semibold rounded-full bg-emerald-500 text-white shadow-md transition transform hover:scale-105';
                document.getElementById('historicalModeBtn').className = 'px-4 py-2 text-xs font-semibold rounded-full bg-white text-gray-900 border border-gray-300 hover:border-purple-400 hover:text-purple-600 transition';
                document.getElementById('liveTimeWindow').classList.remove('hidden');
                document.getElementById('liveIndicator').classList.remove('hidden');
                document.getElementById('dateSelector').classList.add('hidden');
                document.getElementById('timeRangeSelector').classList.add('hidden');
                document.getElementById('historicalIndicator').classList.add('hidden');

                // Clear chart and restart live updates
                clearChartData();
                if (selectedMachineId) {
                    startChartUpdate();
                }
            } else {
                // Show historical controls
                document.getElementById('liveModeBtn').className = 'px-4 py-2 text-xs font-semibold rounded-full bg-white text-gray-900 border border-gray-300 hover:border-sky-400 hover:text-sky-600 transition';
                document.getElementById('historicalModeBtn').className = 'px-4 py-2 text-xs font-semibold rounded-full bg-emerald-500 text-white shadow-md transition transform hover:scale-105';
                document.getElementById('liveTimeWindow').classList.add('hidden');
                document.getElementById('liveIndicator').classList.add('hidden');
                document.getElementById('dateSelector').classList.remove('hidden');
                document.getElementById('timeRangeSelector').classList.remove('hidden');
                document.getElementById('historicalIndicator').classList.remove('hidden');

                // Auto-set date picker to latest sample date if available
                if (latestSampleTimestamp) {
                    const d = new Date(latestSampleTimestamp);
                    const isoDate = d.toISOString().slice(0, 10);
                    document.getElementById('chartDatePicker').value = isoDate;
                }

                // Stop live updates and load historical data
                stopChartUpdate();
                if (selectedMachineId) {
                    loadHistoricalData();
                }
            }
        }

        // Date Picker Change
        document.getElementById('chartDatePicker').addEventListener('change', function() {
            if (chartMode === 'historical' && selectedMachineId) {
                loadHistoricalData();
            }
        });

        // Historical Time Range Change
        document.getElementById('historicalTimeRange').addEventListener('change', function() {
            if (chartMode === 'historical' && selectedMachineId) {
                loadHistoricalData();
            }
        });

        // Time Window Selector Change
        document.getElementById('timeWindowSelector').addEventListener('change', function() {
            timeWindow = parseInt(this.value);
            // Clear and reinitialize chart data
            clearChartData();
        });

        function clearChartData() {
            chartData.labels = [];
            chartData.ax = [];
            chartData.ay = [];
            chartData.az = [];
            if (multiAxisChart) {
                multiAxisChart.data.labels = [];
                multiAxisChart.data.datasets[0].data = [];
                multiAxisChart.data.datasets[1].data = [];
                multiAxisChart.data.datasets[2].data = [];
                multiAxisChart.update();
            }
        }

        // Machine Selector Change
        document.getElementById('machineSelector').addEventListener('change', function() {
            selectedMachineId = this.value;

            if (selectedMachineId) {
                // Reset statistics
                resetStatistics();

                // Show all sections
                document.getElementById('machineStatusCard').classList.remove('hidden');
                document.getElementById('quickStatsSection').classList.remove('hidden');
                document.getElementById('sensorValuesSection').classList.remove('hidden');
                document.getElementById('statsAlertsSection').classList.remove('hidden');
                document.getElementById('chartSection').classList.remove('hidden');
                document.getElementById('liveFeedSection').classList.remove('hidden');

                // Load data
                loadMachineData(selectedMachineId);
                fetchAlerts(selectedMachineId);
                startAutoUpdate();
                connectWebSocket(selectedMachineId);
                initializeChart();
                startChartUpdate();
            } else {
                stopAutoUpdate();
                stopChartUpdate();
                disconnectWebSocket();
                document.getElementById('machineStatusCard').classList.add('hidden');
                document.getElementById('quickStatsSection').classList.add('hidden');
                document.getElementById('sensorValuesSection').classList.add('hidden');
                document.getElementById('statsAlertsSection').classList.add('hidden');
                document.getElementById('chartSection').classList.add('hidden');
                document.getElementById('liveFeedSection').classList.add('hidden');
            }
        });

        function loadMachineData(machineId) {
            fetch(`/api/machine/${machineId}/sensor-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMachineStatus(data.machine);
                        document.getElementById('machineStatusCard').classList.remove('hidden');
                        document.getElementById('sensorValuesSection').classList.remove('hidden');
                        document.getElementById('chartSection').classList.remove('hidden');
                        document.getElementById('liveFeedSection').classList.remove('hidden');

                        // Update real-time sensor values from latest data
                        if (data.sensor_data && data.sensor_data.length > 0) {
                            const latest = data.sensor_data[0];
                            latestSampleTimestamp = latest.timestamp; // remember latest sample time for historical default
                            const sensorValues = {
                                ax: parseFloat(latest.acceleration_x),
                                ay: parseFloat(latest.acceleration_y),
                                az: parseFloat(latest.acceleration_z),
                                temperature: Math.random() * 80 // TODO: Add real temperature from database
                            };
                            updateSensorValues(sensorValues);

                            // Update live feed table
                            updateLiveFeed(data.sensor_data);

                            // Add to chart
                            addChartDataPoint(sensorValues.ax, sensorValues.ay, sensorValues.az);

                            // Update statistics
                            updateStatistics(sensorValues);

                            // Track anomaly status
                            if (machine.status === 'NORMAL') {
                                statistics.normalCount++;
                            } else {
                                statistics.anomalyCount++;
                                statistics.lastAnomaly = new Date();
                            }

                            // Update quick stats display
                            updateQuickStats();
                        }
                    }
                })
                .catch(error => console.error('Error loading machine data:', error));
        }

        function updateMachineStatus(machine) {
            const isNormal = machine.status === 'NORMAL';
            const statusBar = document.getElementById('statusBar');
            const statusBadge = document.getElementById('statusBadge');
            const statusIcon = document.getElementById('statusIcon');

            // Update basic info
            document.getElementById('machineName').textContent = machine.name;
            document.getElementById('machineLocation').textContent = `Location: ${machine.location}`;
            document.getElementById('rmsValue').textContent = machine.rms.toFixed(4);
            document.getElementById('peakValue').textContent = machine.peak_amp.toFixed(4);
            document.getElementById('freqValue').textContent = `${machine.dominant_freq.toFixed(0)} Hz`;
            document.getElementById('lastCheck').textContent = machine.last_check;

            // Update status styling
            if (isNormal) {
                statusBar.className = 'h-2 bg-emerald-500';
                statusBadge.className = 'px-4 py-2 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800';
                statusBadge.textContent = '✓ NORMAL';
                statusIcon.className = 'w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center';
                statusIcon.innerHTML = '<svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            } else {
                statusBar.className = 'h-2 bg-red-500';
                statusBadge.className = 'px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800';
                statusBadge.textContent = '⚠ ANOMALI';
                statusIcon.className = 'w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center';
                statusIcon.innerHTML = '<svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            }
        }

        function updateSensorData(sensorData) {
            const tbody = document.getElementById('sensorDataBody');

            if (!sensorData || sensorData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No sensor data available</td></tr>';
                return;
            }

            tbody.innerHTML = sensorData.map(data => `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${data.timestamp}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">${data.acceleration_x}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">${data.acceleration_y}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">${data.acceleration_z}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">${data.time_ago}</td>
                </tr>
            `).join('');
        }

        // Live feed updater (scrollable latest 10-20 points)
        function updateLiveFeed(sensorData) {
            const tbody = document.getElementById('liveFeedBody');
            const container = document.getElementById('liveFeedContainer');

            if (!sensorData || sensorData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No live data</td></tr>';
                return;
            }

            // Keep only latest 20 entries
            const latest = sensorData.slice(0, 20);

            tbody.innerHTML = latest.map(data => {
                const temp = data.temperature !== undefined && data.temperature !== null
                    ? parseFloat(data.temperature).toFixed(1)
                    : '-';
                return `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">${data.timestamp}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-900">${data.acceleration_x}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-900">${data.acceleration_y}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-900">${data.acceleration_z}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-900">${temp}</td>
                    </tr>
                `;
            }).join('');

            // Auto-scroll to latest
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }

        function startAutoUpdate() {
            stopAutoUpdate();
            updateInterval = setInterval(() => {
                if (selectedMachineId) {
                    loadMachineData(selectedMachineId);
                    fetchAlerts(selectedMachineId);
                }
            }, 5000); // Update every 5 seconds
        }

        function stopAutoUpdate() {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        }

        // WebSocket Connection
        function connectWebSocket(machineId) {
            disconnectWebSocket();

            try {
                wsConnection = new WebSocket('ws://localhost:8080/app/{{ config("app.reverb_app_key") }}?protocol=7&client=js&version=8.4.0-rc2&flash=false');

                wsConnection.onopen = function() {
                    console.log('WebSocket connected for machine:', machineId);

                    // Subscribe to machine channel
                    const subscribeMsg = JSON.stringify({
                        event: 'pusher:subscribe',
                        data: {
                            auth: '',
                            channel: `machine.${machineId}`
                        }
                    });
                    wsConnection.send(subscribeMsg);
                };

                wsConnection.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);

                        if (data.event === 'sensor.update') {
                            const sensorData = JSON.parse(data.data);
                            updateSensorValues(sensorData);
                        }
                    } catch (e) {
                        console.error('Error parsing WebSocket message:', e);
                    }
                };

                wsConnection.onerror = function(error) {
                    console.error('WebSocket error:', error);
                };

                wsConnection.onclose = function() {
                    console.log('WebSocket disconnected');
                };
            } catch (error) {
                console.error('Error connecting WebSocket:', error);
            }
        }

        function disconnectWebSocket() {
            if (wsConnection) {
                wsConnection.close();
                wsConnection = null;
            }
        }

        // Update Real-time Sensor Values
        function updateSensorValues(data) {
            // Update AX
            updateAccelerationCard('ax', data.ax || 0);

            // Update AY
            updateAccelerationCard('ay', data.ay || 0);

            // Update AZ
            updateAccelerationCard('az', data.az || 0);

            // Update Temperature
            updateTemperatureCard(data.temperature || 25);
        }

        function updateAccelerationCard(axis, value) {
            const absValue = Math.abs(value);
            const valueElem = document.getElementById(`${axis}Value`);
            const barElem = document.getElementById(`${axis}Bar`);
            const statusElem = document.getElementById(`${axis}Status`);
            const cardElem = document.getElementById(`${axis}Card`);
            const iconElem = document.getElementById(`${axis}Icon`);

            // Update value
            valueElem.textContent = value.toFixed(4);

            // Calculate percentage (max 2G for display)
            const percentage = Math.min((absValue / 2) * 100, 100);
            barElem.style.width = percentage + '%';

            // Determine status
            let status, colors;
            if (absValue >= THRESHOLDS.acceleration.warning) {
                status = 'CRITICAL';
                colors = {
                    card: 'from-red-50 to-red-100 border-red-400',
                    bar: 'bg-red-500',
                    text: 'text-red-700',
                    icon: 'bg-red-100',
                    iconColor: 'text-red-600'
                };
            } else if (absValue >= THRESHOLDS.acceleration.normal) {
                status = 'WARNING';
                colors = {
                    card: 'from-yellow-50 to-yellow-100 border-yellow-400',
                    bar: 'bg-yellow-500',
                    text: 'text-yellow-700',
                    icon: 'bg-yellow-100',
                    iconColor: 'text-yellow-600'
                };
            } else {
                status = 'NORMAL';
                colors = {
                    card: 'from-emerald-50 to-emerald-100 border-emerald-400',
                    bar: 'bg-emerald-500',
                    text: 'text-emerald-700',
                    icon: 'bg-emerald-100',
                    iconColor: 'text-emerald-600'
                };
            }

            // Update styling
            cardElem.className = `bg-gradient-to-br ${colors.card} rounded-xl p-6 border-2 transition-all duration-300`;
            barElem.className = `shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center ${colors.bar} transition-all duration-500`;
            statusElem.className = `text-xs font-semibold ${colors.text} mt-2 text-center`;
            statusElem.textContent = status;
            iconElem.className = `w-12 h-12 rounded-lg ${colors.icon} flex items-center justify-center`;
            iconElem.querySelector('svg').className = `w-6 h-6 ${colors.iconColor}`;
        }

        function updateTemperatureCard(temperature) {
            const valueElem = document.getElementById('tempValue');
            const barElem = document.getElementById('tempBar');
            const statusElem = document.getElementById('tempStatus');
            const iconElem = document.getElementById('tempIcon');

            // Update value
            valueElem.textContent = temperature.toFixed(1);

            // Calculate percentage (max 100°C for display)
            const percentage = Math.min((temperature / 100) * 100, 100);
            barElem.style.width = percentage + '%';

            // Determine status
            let status, colors;
            if (temperature >= THRESHOLDS.temperature.warning) {
                status = 'CRITICAL';
                colors = {
                    bar: 'bg-red-500',
                    text: 'text-red-700 border-red-400',
                    icon: 'bg-red-100',
                    iconColor: 'text-red-600'
                };
            } else if (temperature >= THRESHOLDS.temperature.normal) {
                status = 'WARNING';
                colors = {
                    bar: 'bg-yellow-500',
                    text: 'text-yellow-700 border-yellow-400',
                    icon: 'bg-yellow-100',
                    iconColor: 'text-yellow-600'
                };
            } else {
                status = 'NORMAL';
                colors = {
                    bar: 'bg-emerald-500',
                    text: 'text-emerald-700 border-emerald-400',
                    icon: 'bg-emerald-100',
                    iconColor: 'text-emerald-600'
                };
            }

            // Update styling
            barElem.className = `shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center ${colors.bar} transition-all duration-500`;
            statusElem.className = `text-sm font-bold px-4 py-2 bg-white rounded-lg border-2 ${colors.text}`;
            statusElem.textContent = status;
            iconElem.className = `w-16 h-16 rounded-xl ${colors.icon} flex items-center justify-center`;
            iconElem.querySelector('svg').className = `w-8 h-8 ${colors.iconColor}`;
        }

        // Multi-Axis Chart Functions
        function initializeChart() {
            const ctx = document.getElementById('multiAxisChart');
            if (!ctx) return;

            // Destroy existing chart if any
            if (multiAxisChart) {
                multiAxisChart.destroy();
            }

            multiAxisChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Axis X',
                            data: [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Axis Y',
                            data: [],
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Axis Z',
                            data: [],
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 4
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
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(4) + ' G';
                                }
                            }
                        },
                        annotation: {
                            annotations: {
                                warningLineTop: {
                                    type: 'line',
                                    yMin: THRESHOLDS.acceleration.normal,
                                    yMax: THRESHOLDS.acceleration.normal,
                                    borderColor: 'rgb(234, 179, 8)',
                                    borderWidth: 2,
                                    borderDash: [6, 6],
                                    label: {
                                        display: false
                                    }
                                },
                                warningLineBottom: {
                                    type: 'line',
                                    yMin: -THRESHOLDS.acceleration.normal,
                                    yMax: -THRESHOLDS.acceleration.normal,
                                    borderColor: 'rgb(234, 179, 8)',
                                    borderWidth: 2,
                                    borderDash: [6, 6],
                                    label: {
                                        display: false
                                    }
                                },
                                criticalLineTop: {
                                    type: 'line',
                                    yMin: THRESHOLDS.acceleration.warning,
                                    yMax: THRESHOLDS.acceleration.warning,
                                    borderColor: 'rgb(239, 68, 68)',
                                    borderWidth: 2,
                                    borderDash: [6, 6],
                                    label: {
                                        display: false
                                    }
                                },
                                criticalLineBottom: {
                                    type: 'line',
                                    yMin: -THRESHOLDS.acceleration.warning,
                                    yMax: -THRESHOLDS.acceleration.warning,
                                    borderColor: 'rgb(239, 68, 68)',
                                    borderWidth: 2,
                                    borderDash: [6, 6],
                                    label: {
                                        display: false
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Time',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Acceleration (G)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toFixed(2) + ' G';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 0
                    }
                }
            });
        }

        function addChartDataPoint(ax, ay, az) {
            if (!multiAxisChart) return;

            const now = new Date();
            const timeLabel = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            // Add new data
            chartData.labels.push(timeLabel);
            chartData.ax.push(ax);
            chartData.ay.push(ay);
            chartData.az.push(az);

            // Calculate max data points based on time window
            const maxPoints = timeWindow; // 1 point per second

            // Remove old data (sliding window)
            if (chartData.labels.length > maxPoints) {
                chartData.labels.shift();
                chartData.ax.shift();
                chartData.ay.shift();
                chartData.az.shift();
            }

            // Update chart
            multiAxisChart.data.labels = chartData.labels;
            multiAxisChart.data.datasets[0].data = chartData.ax;
            multiAxisChart.data.datasets[1].data = chartData.ay;
            multiAxisChart.data.datasets[2].data = chartData.az;
            multiAxisChart.update('none'); // No animation for real-time
        }

        function startChartUpdate() {
            stopChartUpdate();

            // Only start if in live mode
            if (chartMode !== 'live') return;

            // Update chart every 1 second with new data
            chartUpdateInterval = setInterval(() => {
                if (selectedMachineId && multiAxisChart && chartMode === 'live') {
                    // Fetch latest sensor data point
                    fetch(`/api/machine/${selectedMachineId}/sensor-data`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.sensor_data && data.sensor_data.length > 0) {
                                const latest = data.sensor_data[0];
                                addChartDataPoint(
                                    parseFloat(latest.acceleration_x),
                                    parseFloat(latest.acceleration_y),
                                    parseFloat(latest.acceleration_z)
                                );
                            }
                        })
                        .catch(error => console.error('Error updating chart:', error));
                }
            }, 1000); // Update every second
        }

        function loadHistoricalData() {
            if (!selectedMachineId) return;

            const selectedDate = document.getElementById('chartDatePicker').value;
            const timeRange = document.getElementById('historicalTimeRange').value;

            // Clear existing data
            clearChartData();

            // Fetch historical data from API
            fetch(`/api/machine/${selectedMachineId}/historical-data?date=${selectedDate}&hours=${timeRange}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.sensor_data && data.sensor_data.length > 0) {
                        // Populate chart with historical data
                        data.sensor_data.forEach(sample => {
                            const timestamp = new Date(sample.timestamp);
                            const timeLabel = timestamp.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            chartData.labels.push(timeLabel);
                            chartData.ax.push(parseFloat(sample.acceleration_x));
                            chartData.ay.push(parseFloat(sample.acceleration_y));
                            chartData.az.push(parseFloat(sample.acceleration_z));
                        });

                        // Update chart
                        if (multiAxisChart) {
                            multiAxisChart.data.labels = chartData.labels;
                            multiAxisChart.data.datasets[0].data = chartData.ax;
                            multiAxisChart.data.datasets[1].data = chartData.ay;
                            multiAxisChart.data.datasets[2].data = chartData.az;
                            multiAxisChart.update();
                        }
                    } else {
                        console.warn('No historical data available for selected date and range', data.date_range);
                        if (multiAxisChart) {
                            clearChartData();
                            multiAxisChart.update();
                        }
                    }
                })
                .catch(error => console.error('Error loading historical data:', error));
        }

        function stopChartUpdate() {
            if (chartUpdateInterval) {
                clearInterval(chartUpdateInterval);
                chartUpdateInterval = null;
            }
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            stopAutoUpdate();
            stopChartUpdate();
            disconnectWebSocket();
        });

        // === Statistics Functions ===
        function updateStatistics(axisData) {
            const axes = ['ax', 'ay', 'az'];

            axes.forEach(axis => {
                const value = parseFloat(axisData[axis]);
                if (isNaN(value)) return;

                const stat = statistics[axis];

                // Update min/max
                stat.min = Math.min(stat.min, value);
                stat.max = Math.max(stat.max, value);

                // Update sum and count for average
                stat.sum += value;
                stat.count++;

                // Keep last 100 values
                stat.values.push(value);
                if (stat.values.length > 100) {
                    stat.values.shift();
                }

                // Detect peak (exceeds warning threshold)
                if (Math.abs(value) >= THRESHOLDS.acceleration.warning) {
                    const timestamp = new Date().toLocaleTimeString('id-ID');
                    const severity = Math.abs(value) >= THRESHOLDS.acceleration.warning ? 'CRITICAL' : 'WARNING';
                    stat.peak = { value, timestamp, severity };
                }
            });

            // Update total readings
            statistics.totalReadings++;

            // Update DOM
            updateStatisticsDisplay();
        }

        function updateStatisticsDisplay() {
            const axes = [
                { key: 'ax', label: 'X-Axis', color: 'blue' },
                { key: 'ay', label: 'Y-Axis', color: 'green' },
                { key: 'az', label: 'Z-Axis', color: 'purple' }
            ];

            axes.forEach(({ key, label }) => {
                const stat = statistics[key];

                // Min/Max/Avg
                const min = stat.min === Infinity ? 0 : stat.min.toFixed(4);
                const max = stat.max === -Infinity ? 0 : stat.max.toFixed(4);
                const avg = stat.count > 0 ? (stat.sum / stat.count).toFixed(4) : '0.0000';

                document.getElementById(`stat${key.charAt(0).toUpperCase() + key.slice(1)}Min`).textContent = `${min} G`;
                document.getElementById(`stat${key.charAt(0).toUpperCase() + key.slice(1)}Max`).textContent = `${max} G`;
                document.getElementById(`stat${key.charAt(0).toUpperCase() + key.slice(1)}Avg`).textContent = `${avg} G`;

                // Peak
                const peakElem = document.getElementById(`stat${key.charAt(0).toUpperCase() + key.slice(1)}Peak`);
                if (stat.peak) {
                    peakElem.textContent = `Peak: ${stat.peak.value.toFixed(4)}G at ${stat.peak.timestamp} (${stat.peak.severity})`;
                    peakElem.className = stat.peak.severity === 'CRITICAL' ? 'text-sm text-red-600 font-semibold' : 'text-sm text-yellow-600 font-semibold';
                } else {
                    peakElem.textContent = 'No peaks detected';
                    peakElem.className = 'text-sm text-gray-500';
                }
            });
        }

        function resetStatistics() {
            statistics.ax = { min: Infinity, max: -Infinity, sum: 0, count: 0, peak: null, values: [] };
            statistics.ay = { min: Infinity, max: -Infinity, sum: 0, count: 0, peak: null, values: [] };
            statistics.az = { min: Infinity, max: -Infinity, sum: 0, count: 0, peak: null, values: [] };
            statistics.totalReadings = 0;
            statistics.normalCount = 0;
            statistics.anomalyCount = 0;
            statistics.lastAnomaly = null;
            updateStatisticsDisplay();
            updateQuickStats();
        }

        // === Quick Stats Functions ===
        function updateQuickStats() {
            // Total readings today
            document.getElementById('statReadings').textContent = statistics.totalReadings;

            // Uptime percentage (normal / total * 100)
            const uptime = statistics.totalReadings > 0
                ? ((statistics.normalCount / statistics.totalReadings) * 100).toFixed(1)
                : '0.0';
            document.getElementById('statUptime').textContent = `${uptime}%`;

            // Last anomaly
            const lastAnomalyElem = document.getElementById('statLastAnomaly');
            if (statistics.lastAnomaly) {
                lastAnomalyElem.textContent = new Date(statistics.lastAnomaly).toLocaleString('id-ID');
                lastAnomalyElem.className = 'text-2xl font-bold text-orange-600';
            } else {
                lastAnomalyElem.textContent = 'No anomalies';
                lastAnomalyElem.className = 'text-2xl font-bold text-green-600';
            }

            // Normal time percentage
            const normalTime = statistics.totalReadings > 0
                ? ((statistics.normalCount / statistics.totalReadings) * 100).toFixed(1)
                : '100.0';
            document.getElementById('statNormalTime').textContent = `${normalTime}%`;
        }

        // === Alert Functions ===
        async function fetchAlerts(machineId) {
            try {
                const response = await fetch(`/api/machine/${machineId}/alerts`);
                const data = await response.json();

                if (data.success && data.alerts) {
                    displayAlerts(data.alerts);
                }
            } catch (error) {
                console.error('Error fetching alerts:', error);
            }
        }

        function displayAlerts(alerts) {
            const alertList = document.getElementById('alertList');
            const alertCount = document.getElementById('alertCount');

            alertCount.textContent = alerts.length;

            if (alerts.length === 0) {
                alertList.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-lg font-medium">No active alerts</p>
                        <p class="text-sm">System operating normally</p>
                    </div>
                `;
                return;
            }

            alertList.innerHTML = alerts.map(alert => {
                const severityColors = {
                    'CRITICAL': 'bg-red-50 border-red-300 text-red-800',
                    'WARNING': 'bg-yellow-50 border-yellow-300 text-yellow-800',
                    'INFO': 'bg-blue-50 border-blue-300 text-blue-800'
                };
                const color = severityColors[alert.severity] || severityColors['INFO'];

                return `
                    <div class="p-4 rounded-lg border-2 ${color}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-1 text-xs font-bold rounded ${alert.severity === 'CRITICAL' ? 'bg-red-200' : alert.severity === 'WARNING' ? 'bg-yellow-200' : 'bg-blue-200'}">
                                        ${alert.severity}
                                    </span>
                                    <span class="text-xs text-gray-600">${new Date(alert.created_at).toLocaleString('id-ID')}</span>
                                </div>
                                <p class="text-sm font-medium">${alert.message}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // === Export Functions ===
        document.getElementById('exportDataBtn').addEventListener('click', function() {
            exportToCSV();
        });

        function exportToCSV() {
            if (!selectedMachineId) {
                alert('Please select a machine first');
                return;
            }

            const machine = Array.from(document.getElementById('machineSelect').options)
                .find(opt => opt.value == selectedMachineId);
            const machineName = machine ? machine.textContent.trim() : 'Unknown';

            // CSV Headers
            let csv = 'Timestamp,Acceleration X (G),Acceleration Y (G),Acceleration Z (G),Temperature (°C)\n';

            // Use chart data for export
            if (chartData.labels.length === 0) {
                alert('No data available to export');
                return;
            }

            // Add data rows
            for (let i = 0; i < chartData.labels.length; i++) {
                const row = [
                    chartData.labels[i],
                    chartData.ax[i] || '0',
                    chartData.ay[i] || '0',
                    chartData.az[i] || '0',
                    '0' // Temperature placeholder
                ];
                csv += row.join(',') + '\n';
            }

            // Create blob and download
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            const dateStr = new Date().toISOString().split('T')[0];

            link.setAttribute('href', url);
            link.setAttribute('download', `sensor-data-${machineName}-${dateStr}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // === Threshold Configuration ===
        const thresholdModal = document.getElementById('thresholdModal');
        const thresholdConfigBtn = document.getElementById('thresholdConfigBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelThresholdBtn = document.getElementById('cancelThresholdBtn');
        const applyThresholdBtn = document.getElementById('applyThresholdBtn');
        const warningThresholdInput = document.getElementById('warningThreshold');
        const criticalThresholdInput = document.getElementById('criticalThreshold');
        const warningValueDisplay = document.getElementById('warningValue');
        const criticalValueDisplay = document.getElementById('criticalValue');

        // Open modal
        thresholdConfigBtn.addEventListener('click', function() {
            thresholdModal.classList.remove('hidden');
            // Set current values
            warningThresholdInput.value = THRESHOLDS.acceleration.normal;
            criticalThresholdInput.value = THRESHOLDS.acceleration.warning;
            warningValueDisplay.textContent = THRESHOLDS.acceleration.normal.toFixed(1);
            criticalValueDisplay.textContent = THRESHOLDS.acceleration.warning.toFixed(1);
        });

        // Close modal
        [closeModalBtn, cancelThresholdBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                thresholdModal.classList.add('hidden');
            });
        });

        // Update value displays
        warningThresholdInput.addEventListener('input', function() {
            warningValueDisplay.textContent = parseFloat(this.value).toFixed(1);
        });

        criticalThresholdInput.addEventListener('input', function() {
            criticalValueDisplay.textContent = parseFloat(this.value).toFixed(1);
        });

        // Apply thresholds
        applyThresholdBtn.addEventListener('click', function() {
            const newWarning = parseFloat(warningThresholdInput.value);
            const newCritical = parseFloat(criticalThresholdInput.value);

            // Validation
            if (newWarning >= newCritical) {
                alert('Warning threshold must be less than critical threshold');
                return;
            }

            // Update thresholds
            THRESHOLDS.acceleration.normal = newWarning;
            THRESHOLDS.acceleration.warning = newCritical;

            // Update chart annotations
            if (multiAxisChart && multiAxisChart.options.plugins.annotation) {
                multiAxisChart.options.plugins.annotation.annotations.warningLineTop.yMin = newWarning;
                multiAxisChart.options.plugins.annotation.annotations.warningLineTop.yMax = newWarning;
                multiAxisChart.options.plugins.annotation.annotations.warningLineBottom.yMin = -newWarning;
                multiAxisChart.options.plugins.annotation.annotations.warningLineBottom.yMax = -newWarning;
                multiAxisChart.options.plugins.annotation.annotations.criticalLineTop.yMin = newCritical;
                multiAxisChart.options.plugins.annotation.annotations.criticalLineTop.yMax = newCritical;
                multiAxisChart.options.plugins.annotation.annotations.criticalLineBottom.yMin = -newCritical;
                multiAxisChart.options.plugins.annotation.annotations.criticalLineBottom.yMax = -newCritical;
                multiAxisChart.update();
            }

            // Save to localStorage
            localStorage.setItem('accelerationThresholds', JSON.stringify({
                normal: newWarning,
                warning: newCritical
            }));

            // Close modal
            thresholdModal.classList.add('hidden');

            alert('Thresholds updated successfully');
        });

        // Load saved thresholds on page load
        const savedThresholds = localStorage.getItem('accelerationThresholds');
        if (savedThresholds) {
            const thresholds = JSON.parse(savedThresholds);
            THRESHOLDS.acceleration.normal = thresholds.normal;
            THRESHOLDS.acceleration.warning = thresholds.warning;
        }

        }); // End DOMContentLoaded

    </script>
</x-app-layout>
