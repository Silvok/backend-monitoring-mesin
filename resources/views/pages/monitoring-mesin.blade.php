<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endpush
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h2 class="font-bold text-2xl text-emerald-900 tracking-tight">
                    Monitoring & Analisis Mesin
                </h2>
            </div>

            <!-- Module Switcher -->
            <div class="flex bg-gray-100 p-1.5 rounded-xl border border-gray-200 shadow-sm">
                <button onclick="switchModule('grafik')" id="btn-grafik"
                    class="flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 bg-white text-emerald-600 shadow-sm border border-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span>Grafik</span>
                </button>
                <button onclick="switchModule('analisis')" id="btn-analisis"
                    class="flex items-center space-x-2 px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300 text-gray-500 hover:text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span>Analisis</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Global Filter Card -->
            <div class="bg-white shadow-sm border border-emerald-100 overflow-hidden" style="border-radius: 1rem;">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-3 border-b border-emerald-100"
                    style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider">Global Filter</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <!-- Machine Selector -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Mesin / Node
                                ESP</label>
                            <select id="filter-machine"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Time Range -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Rentang
                                Waktu</label>
                            <select id="filter-time-range" onchange="toggleCustomRange()"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="realtime">Real-time</option>
                                <option value="1h">1 Jam Terakhir</option>
                                <option value="24h" selected>24 Jam Terakhir</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>

                        <!-- Vibration Axis -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Axis
                                Getaran</label>
                            <select id="filter-axis"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="x">Sumbu X</option>
                                <option value="y">Sumbu Y</option>
                                <option value="z">Sumbu Z</option>
                                <option value="resultant" selected>Resultant (Total)</option>
                            </select>
                        </div>

                        <!-- Data Type -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Jenis
                                Data</label>
                            <select id="filter-data-type"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="raw">Raw Signal</option>
                                <option value="rms" selected>RMS</option>
                                <option value="fft">FFT</option>
                            </select>
                        </div>

                        <!-- Sampling Window -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Sampling
                                Window</label>
                            <select id="filter-window"
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm font-medium">
                                <option value="1s">1 Detik</option>
                                <option value="5s" selected>5 Detik</option>
                                <option value="10s">10 Detik</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom Range Inputs (Hidden by default) -->
                    <div id="custom-range-inputs"
                        class="hidden mt-6 pt-6 border-t border-gray-100 flex flex-wrap gap-4 items-end animate-fade-in">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Dari
                                Tanggal</label>
                            <input type="datetime-local" id="filter-start"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-tight">Sampai
                                Tanggal</label>
                            <input type="datetime-local" id="filter-end"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <button onclick="applyFilter()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-1.5 px-4 rounded-lg transition-all shadow-sm hover:shadow-md text-xs">
                            Apply Custom Range
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div id="module-container" class="space-y-6"></div>

        </div>
    </div>

    @push('scripts')
        <script>
            function toggleCustomRange() {
                const timeRange = document.getElementById('filter-time-range').value;
                const customRangeDiv = document.getElementById('custom-range-inputs');

                if (timeRange === 'custom') {
                    customRangeDiv.classList.remove('hidden');
                    customRangeDiv.classList.add('flex');
                } else {
                    customRangeDiv.classList.add('hidden');
                    customRangeDiv.classList.remove('flex');
                }
            }

            function applyFilter() {
                const machineId = document.getElementById('filter-machine').value;
                if (!machineId) {
                    alert('Mohon pilih mesin terlebih dahulu.');
                    return;
                }
                console.log('Filter applied for machine:', machineId);
                // Content loading logic will be re-added here
            }
        </script>
    @endpush

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</x-app-layout>