<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <h2 class="font-bold text-xl text-emerald-900 leading-tight">
                    Monitoring Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div
                    class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Live</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div
                    class="hidden sm:block text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold"
                        id="currentTime">{{ now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            function startClock() {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                function updateTime() {
                    const now = new Date();
                    const dayName = days[now.getDay()];
                    const day = String(now.getDate()).padStart(2, '0');
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');

                    const timeString = `${dayName}, ${day} ${month} ${year}, ${hours}:${minutes}`;
                    const timeElement = document.getElementById('currentTime');
                    if (timeElement) {
                        timeElement.textContent = timeString;
                    }
                }

                updateTime(); // Update immediately
                setInterval(updateTime, 60000); // Update every minute
            }

            document.addEventListener('DOMContentLoaded', startClock);
        </script>
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ============================================== --}}
            {{-- CARD: ANALISIS TREN - EARLY WARNING DETECTION --}}
            {{-- ============================================== --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-gray-100">
                <!-- Header dengan gradient -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-100 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Analisis Tren RMS</h3>
                                <p class="text-sm text-gray-500">Early Warning Detection System</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">24 Jam Terakhir</span>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Alert Box - Trend Warning --}}
                    @php
                        $trendDirection = $analysisInsights['trendAnalysis']['direction'] ?? 'Stabil';
                        $changePercent = $analysisInsights['trendAnalysis']['change_percent'] ?? 0;
                        $isSignificant = $analysisInsights['trendAnalysis']['is_significant'] ?? false;
                        $machineStatus = $analysisInsights['machineStatus'] ?? 'NORMAL';

                        // Determine alert type
                        $alertType = 'info';
                        $alertBg = 'bg-blue-50 border-blue-200';
                        $alertIcon = 'text-blue-500';
                        $alertText = 'text-blue-800';

                        if ($changePercent > 20) {
                            $alertType = 'danger';
                            $alertBg = 'bg-red-50 border-red-200';
                            $alertIcon = 'text-red-500';
                            $alertText = 'text-red-800';
                        } elseif ($changePercent > 10) {
                            $alertType = 'warning';
                            $alertBg = 'bg-amber-50 border-amber-200';
                            $alertIcon = 'text-amber-500';
                            $alertText = 'text-amber-800';
                        } elseif ($changePercent < -10) {
                            $alertType = 'success';
                            $alertBg = 'bg-emerald-50 border-emerald-200';
                            $alertIcon = 'text-emerald-500';
                            $alertText = 'text-emerald-800';
                        }
                    @endphp

                    {{-- Main Alert Message --}}
                    <div class="mb-6 p-4 rounded-xl border-2 {{ $alertBg }} flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if($alertType === 'danger')
                                <svg class="w-8 h-8 {{ $alertIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            @elseif($alertType === 'warning')
                                <svg class="w-8 h-8 {{ $alertIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($alertType === 'success')
                                <svg class="w-8 h-8 {{ $alertIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-8 h-8 {{ $alertIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold {{ $alertText }} text-lg mb-1">
                                @if($changePercent > 0)
                                    ⚠️ Terjadi peningkatan RMS sebesar {{ abs($changePercent) }}% dalam 24 jam terakhir.
                                @elseif($changePercent < 0)
                                    ✅ Terjadi penurunan RMS sebesar {{ abs($changePercent) }}% dalam 24 jam terakhir.
                                @else
                                    ℹ️ Nilai RMS stabil dalam 24 jam terakhir.
                                @endif
                            </h4>
                            <p class="text-sm {{ $alertText }} opacity-80">
                                @if($changePercent > 20)
                                    <strong>KRITIS:</strong> Peningkatan signifikan terdeteksi! Segera lakukan inspeksi mesin.
                                @elseif($changePercent > 10)
                                    <strong>PERINGATAN:</strong> Tren kenaikan getaran perlu diperhatikan. Jadwalkan pemeriksaan.
                                @elseif($changePercent < -10)
                                    <strong>MEMBAIK:</strong> Kondisi mesin menunjukkan perbaikan. Lanjutkan pemantauan.
                                @else
                                    <strong>STABIL:</strong> Tidak ada perubahan signifikan. Lanjutkan operasi normal.
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <div class="text-3xl font-black {{ $alertText }}">
                                {{ $changePercent > 0 ? '+' : '' }}{{ $changePercent }}%
                            </div>
                            <div class="text-xs {{ $alertText }} opacity-60">Perubahan RMS</div>
                        </div>
                    </div>

                    {{-- Stats Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        {{-- Current RMS --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-xs font-medium text-gray-500 uppercase">RMS Saat Ini</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">
                                {{ number_format($analysisInsights['stats']['avg'] ?? 0, 4) }} <span class="text-sm font-normal text-gray-500">g</span>
                            </div>
                        </div>

                        {{-- Max RMS --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-xs font-medium text-gray-500 uppercase">RMS Maksimum</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">
                                {{ number_format($analysisInsights['stats']['max'] ?? 0, 4) }} <span class="text-sm font-normal text-gray-500">g</span>
                            </div>
                        </div>

                        {{-- Min RMS --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="text-xs font-medium text-gray-500 uppercase">RMS Minimum</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">
                                {{ number_format($analysisInsights['stats']['min'] ?? 0, 4) }} <span class="text-sm font-normal text-gray-500">g</span>
                            </div>
                        </div>

                        {{-- Trend Direction --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                <span class="text-xs font-medium text-gray-500 uppercase">Arah Tren</span>
                            </div>
                            <div class="text-lg font-bold flex items-center gap-2
                                @if($changePercent > 10) text-red-600
                                @elseif($changePercent < -10) text-emerald-600
                                @else text-gray-600
                                @endif">
                                @if($changePercent > 0)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    Naik
                                @elseif($changePercent < 0)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                                    </svg>
                                    Turun
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                                    </svg>
                                    Stabil
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Trend Chart --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-700">Grafik Tren RMS (7 Hari Terakhir)</h4>
                            <div class="flex items-center gap-4 text-xs">
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                    <span class="text-gray-500">RMS Rata-rata</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <span class="text-gray-500">RMS Maksimum</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative" style="height: 280px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>

                    {{-- Rekomendasi --}}
                    <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <div>
                                <h5 class="font-semibold text-blue-800 mb-1">Rekomendasi Tindakan</h5>
                                <p class="text-sm text-blue-700">{{ $analysisInsights['recommendation'] ?? 'Lanjutkan pemantauan rutin.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================== --}}
            {{-- CARD: STATUS MESIN & THRESHOLD --}}
            {{-- ============================================== --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 rounded-lg">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Status Kondisi Mesin</h3>
                            <p class="text-sm text-gray-500">Berdasarkan ISO 10816-3</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Status Badge --}}
                    <div class="flex items-center justify-center mb-6">
                        <div class="px-8 py-4 rounded-2xl text-center
                            @if($machineStatus === 'CRITICAL') bg-red-100 border-2 border-red-300
                            @elseif($machineStatus === 'WARNING') bg-amber-100 border-2 border-amber-300
                            @else bg-emerald-100 border-2 border-emerald-300
                            @endif">
                            <div class="text-sm font-medium
                                @if($machineStatus === 'CRITICAL') text-red-600
                                @elseif($machineStatus === 'WARNING') text-amber-600
                                @else text-emerald-600
                                @endif mb-1">Status Saat Ini</div>
                            <div class="text-3xl font-black
                                @if($machineStatus === 'CRITICAL') text-red-700
                                @elseif($machineStatus === 'WARNING') text-amber-700
                                @else text-emerald-700
                                @endif">{{ $machineStatus }}</div>
                        </div>
                    </div>

                    {{-- Threshold Bar - ISO 10816-3 Class I (mm/s) --}}
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-2">
                            <span>0 mm/s</span>
                            <span>2.8 mm/s (Warning)</span>
                            <span>7.1 mm/s (Critical)</span>
                            <span>11.2+ mm/s</span>
                        </div>
                        <div class="relative h-6 bg-gray-200 rounded-full overflow-hidden">
                            {{-- Gradient zones based on ISO 10816-3 --}}
                            <div class="absolute inset-0 flex">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500" style="width: 25%"></div>
                                <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500" style="width: 38%"></div>
                                <div class="h-full bg-gradient-to-r from-red-400 to-red-600" style="width: 37%"></div>
                            </div>
                            {{-- Current value marker --}}
                            @php
                                $currentRMS = $analysisInsights['stats']['avg'] ?? 0;
                                $markerPosition = min(($currentRMS / 11.2) * 100, 100);
                            @endphp
                            <div class="absolute top-0 bottom-0 w-1 bg-gray-900 shadow-lg transition-all duration-500"
                                 style="left: {{ $markerPosition }}%;">
                                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap">
                                    {{ number_format($currentRMS, 3) }}g
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kesimpulan --}}
                    <div class="mt-6 p-4 rounded-xl
                        @if($machineStatus === 'CRITICAL') bg-red-50 border border-red-200
                        @elseif($machineStatus === 'WARNING') bg-amber-50 border border-amber-200
                        @else bg-emerald-50 border border-emerald-200
                        @endif">
                        <p class="font-medium
                            @if($machineStatus === 'CRITICAL') text-red-800
                            @elseif($machineStatus === 'WARNING') text-amber-800
                            @else text-emerald-800
                            @endif">
                            📋 {{ $analysisInsights['conclusion'] ?? 'Mesin beroperasi dalam kondisi normal.' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trend Chart Data from Controller
            const trendChartData = @json($trendChartData ?? ['labels' => [], 'avg_values' => [], 'max_values' => []]);

            const ctx = document.getElementById('trendChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendChartData.labels || [],
                        datasets: [
                            {
                                label: 'RMS Rata-rata (mm/s)',
                                data: trendChartData.avg_values || [],
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#10b981',
                            },
                            {
                                label: 'RMS Maksimum (mm/s)',
                                data: trendChartData.max_values || [],
                                borderColor: '#f87171',
                                backgroundColor: 'rgba(248, 113, 113, 0.05)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f87171',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y?.toFixed(4) || 0} g`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    font: { size: 11 }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                title: {
                                    display: true,
                                    text: 'RMS (mm/s)',
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
