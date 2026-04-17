<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-emerald-900 leading-tight">Laporan Bulanan</h2>
                <p class="text-sm text-gray-600">Ringkasan performa mesin per bulan untuk kebutuhan audit dan tindak lanjut.</p>
            </div>
            @php
                $reportQuery = [
                    'month' => $month ?? now()->format('Y-m'),
                    'day' => $day ?? null,
                    'machine_id' => $selectedMachine ?? 'all',
                ];
            @endphp
            <div class="flex items-center gap-2">
                <button form="monthlyReportForm" type="submit"
                        class="px-4 py-2 rounded-lg border border-emerald-200 text-emerald-700 text-sm font-semibold hover:bg-emerald-50">
                    Generate
                </button>
                <button id="downloadPdfBtn" type="button"
                   class="px-4 py-2 rounded-lg border border-emerald-200 text-emerald-700 text-sm font-semibold hover:bg-emerald-50">
                    Unduh PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <form id="monthlyReportForm" method="GET" action="{{ route('monthly-report') }}"
                  class="flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <label class="text-sm font-semibold text-gray-700">Periode</label>
                    <input name="month" type="month" value="{{ $month ?? now()->format('Y-m') }}"
                           class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex-1">
                    <label class="text-sm font-semibold text-gray-700">Tanggal (opsional)</label>
                    <input name="day" type="date" value="{{ $day ?? '' }}"
                           class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex-1">
                    <label class="text-sm font-semibold text-gray-700">Mesin (opsional)</label>
                    <select name="machine_id" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="all" @selected(($selectedMachine ?? 'all') === 'all')>Semua Mesin</option>
                        @foreach(($machines ?? []) as $machine)
                            <option value="{{ $machine->id }}" @selected((string) ($selectedMachine ?? 'all') === (string) $machine->id)>
                                {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-sm font-semibold text-gray-700">Catatan</label>
                    <input type="text" placeholder="Contoh: bulan pengamatan awal" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </form>
            <form id="monthlyReportPdfForm" method="POST" action="{{ route('monthly-report.pdf') }}" target="_blank" class="hidden">
                @csrf
                <input type="hidden" name="month" value="{{ $month ?? now()->format('Y-m') }}">
                <input type="hidden" name="day" value="{{ $day ?? '' }}">
                <input type="hidden" name="machine_id" value="{{ $selectedMachine ?? 'all' }}">
                <input type="hidden" name="chart_abnormal" id="chartAbnormalInput">
                <input type="hidden" name="chart_rms" id="chartRmsInput">
            </form>
            <p class="mt-3 text-xs text-gray-500">Data diambil otomatis berdasarkan periode dan mesin yang dipilih.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl text-white p-4 shadow-sm" style="background: linear-gradient(135deg, #1ea36b, #27c17d);">
                <div class="text-sm" style="color: rgba(255,255,255,0.85);">Total Data</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.85);">Sampel analisis bulan ini</div>
            </div>
            <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm">
                <div class="text-sm text-emerald-700 font-semibold">Normal</div>
                <div class="text-2xl font-bold text-emerald-800 mt-1">{{ number_format($summary['normal'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-emerald-700 mt-1">
                    {{ ($summary['total'] ?? 0) > 0 ? number_format(($summary['normal'] ?? 0) / $summary['total'] * 100, 1) : 0 }}% dari total
                </div>
            </div>
            <div class="rounded-2xl bg-yellow-50 border border-yellow-100 p-4 shadow-sm">
                <div class="text-sm text-yellow-700 font-semibold">Warning</div>
                <div class="text-2xl font-bold text-yellow-800 mt-1">{{ number_format($summary['warning'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-yellow-700 mt-1">
                    {{ ($summary['total'] ?? 0) > 0 ? number_format(($summary['warning'] ?? 0) / $summary['total'] * 100, 1) : 0 }}% dari total
                </div>
            </div>
            <div class="rounded-2xl bg-red-50 border border-red-100 p-4 shadow-sm">
                <div class="text-sm text-red-700 font-semibold">Critical</div>
                <div class="text-2xl font-bold text-red-800 mt-1">{{ number_format($summary['critical'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-red-700 mt-1">
                    {{ ($summary['total'] ?? 0) > 0 ? number_format(($summary['critical'] ?? 0) / $summary['total'] * 100, 1) : 0 }}% dari total
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="font-bold text-gray-900">Ringkasan Eksekutif</h3>
            <p class="mt-2 text-sm text-gray-700 leading-relaxed">
                Pada periode ini, sistem mencatat total <span class="font-semibold text-emerald-700">{{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</span> data
                dengan <span class="font-semibold text-yellow-700">{{ number_format($summary['warning'] ?? 0, 0, ',', '.') }}</span> warning dan
                <span class="font-semibold text-red-700">{{ number_format($summary['critical'] ?? 0, 0, ',', '.') }}</span> critical.
                Secara umum kondisi mesin
                <span class="font-semibold {{ ($totalAbnormal ?? 0) > 0 ? 'text-yellow-700' : 'text-emerald-700' }}">
                    {{ ($totalAbnormal ?? 0) > 0 ? 'terdapat beberapa abnormal' : 'relatif stabil' }}
                </span>.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900">Frekuensi Abnormal Mingguan</h3>
                        <p class="text-xs text-gray-500">Jumlah warning & critical per minggu</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Preview</span>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Keterangan: angka menunjukkan total kejadian abnormal (Warning + Critical) per minggu.
                </p>
                <p id="abnormalWeeklySummary" class="mt-1 text-xs text-emerald-700 font-semibold"></p>
                <p class="mt-1 text-xs text-gray-600">
                    Total abnormal bulan ini: <span class="font-semibold text-gray-800">{{ number_format($totalAbnormal ?? 0, 0, ',', '.') }}</span> kejadian.
                </p>
                <div class="mt-2 h-64">
                    <canvas id="monthlyAbnormalChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-bold text-gray-900">Ringkasan Abnormal</h3>
                <p class="text-xs text-gray-500">Insight utama untuk laporan</p>
                <ul class="mt-4 space-y-3 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-yellow-500"></span>
                        Puncak warning/critical terjadi pada minggu dengan jumlah abnormal tertinggi.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
                        Fokuskan inspeksi pada hari dengan RMS tertinggi.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        Catat tindakan perbaikan untuk evaluasi bulan berikutnya.
                    </li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-bold text-gray-900">Top 3 {{ !empty($day) ? 'Jam' : 'Hari' }} RMS Tertinggi</h3>
                <p class="text-xs text-gray-500">
                    {{ !empty($day) ? 'Rata-rata RMS per 10 menit pada tanggal terpilih.' : 'Rata-rata RMS harian tertinggi pada periode.' }}
                </p>
                <ol class="mt-4 space-y-2 text-sm text-gray-700">
                    @forelse(($topRmsPoints ?? []) as $idx => $row)
                        <li class="flex items-center justify-between">
                            <span class="font-semibold text-gray-800">{{ $idx + 1 }}.</span>
                            <span class="flex-1 ml-2">{{ $row['label'] ?? '-' }}</span>
                            <span class="font-semibold text-emerald-700">{{ number_format($row['avg_rms'] ?? 0, !empty($day) ? 3 : 2) }} mm/s</span>
                        </li>
                    @empty
                        <li class="text-gray-500">Belum ada data RMS pada periode ini.</li>
                    @endforelse
                </ol>
            </div>
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-bold text-gray-900">Catatan Tindakan / Rekomendasi</h3>
                <p class="text-xs text-gray-500">Isi tindakan yang disarankan berdasarkan temuan abnormal.</p>
                <ul class="mt-4 space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        Periksa bearing jika RMS cenderung meningkat pada minggu yang sama.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        Cek alignment dan kekencangan belt pada jam dengan RMS tertinggi.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        Jadwalkan inspeksi ulang bila warning berulang lebih dari 3 kali.
                    </li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-bold text-gray-900">Statistik RMS</h3>
                <p class="text-xs text-gray-500">Min / Max / Rata-rata / Median</p>
                <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="rounded-lg border border-gray-100 p-3">
                        <div class="text-xs text-gray-500">Min</div>
                        <div class="text-lg font-semibold text-gray-900">{{ number_format($rmsStats['min'] ?? 0, 2) }} mm/s</div>
                    </div>
                    <div class="rounded-lg border border-gray-100 p-3">
                        <div class="text-xs text-gray-500">Max</div>
                        <div class="text-lg font-semibold text-gray-900">{{ number_format($rmsStats['max'] ?? 0, 2) }} mm/s</div>
                    </div>
                    <div class="rounded-lg border border-gray-100 p-3">
                        <div class="text-xs text-gray-500">Rata-rata</div>
                        <div class="text-lg font-semibold text-emerald-700">{{ number_format($rmsStats['avg'] ?? 0, 2) }} mm/s</div>
                    </div>
                    <div class="rounded-lg border border-gray-100 p-3">
                        <div class="text-xs text-gray-500">Median</div>
                        <div class="text-lg font-semibold text-gray-900">{{ number_format($rmsStats['median'] ?? 0, 2) }} mm/s</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-bold text-gray-900">Distribusi Status</h3>
                <p class="text-xs text-gray-500">Persentase Normal vs Warning vs Critical</p>
                <div class="mt-4 space-y-3 text-sm text-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-emerald-700">Normal</span>
                        <span class="font-semibold text-emerald-800">{{ number_format($statusDistribution['normal'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-yellow-700">Warning</span>
                        <span class="font-semibold text-yellow-800">{{ number_format($statusDistribution['warning'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-red-700">Critical</span>
                        <span class="font-semibold text-red-800">{{ number_format($statusDistribution['critical'] ?? 0, 1) }}%</span>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-bold text-gray-900">Highlight Abnormal Terbesar</h3>
                <p class="text-xs text-gray-500">Tanggal &amp; RMS tertinggi</p>
                <div class="mt-4 text-sm text-gray-700">
                    @if(!empty($topAbnormal))
                        <div class="font-semibold text-gray-900">{{ $topAbnormal->created_at?->format('Y-m-d H:i') }}</div>
                        <div class="mt-1 text-emerald-700 font-semibold">{{ number_format($topAbnormal->rms ?? 0, 2) }} mm/s</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $topAbnormal->machine?->name ?? '-' }}</div>
                    @else
                        <div class="text-gray-500">Belum ada data abnormal pada periode ini.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="font-bold text-gray-900">Parameter Pengukuran</h3>
            <p class="text-xs text-gray-500">Ringkasan parameter pengambilan data</p>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm text-gray-700">
                <div class="rounded-lg border border-gray-100 p-3">
                    <div class="text-xs text-gray-500">Interval Sampling</div>
                    <div class="font-semibold text-gray-900">{{ $measurementParams['sampling_interval'] ?? 1 }} menit</div>
                </div>
                <div class="rounded-lg border border-gray-100 p-3">
                    <div class="text-xs text-gray-500">Band-pass</div>
                    <div class="font-semibold text-gray-900">{{ $measurementParams['band_pass'] ?? '10–500 Hz' }}</div>
                </div>
                <div class="rounded-lg border border-gray-100 p-3">
                    <div class="text-xs text-gray-500">Satuan</div>
                    <div class="font-semibold text-gray-900">{{ $measurementParams['unit'] ?? 'mm/s' }}</div>
                </div>
                <div class="rounded-lg border border-gray-100 p-3">
                    <div class="text-xs text-gray-500">Tanggal Data</div>
                    <div class="font-semibold text-gray-900">{{ $measurementParams['period_label'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">
                        {{ !empty($day) ? 'Rata-rata RMS per 10 Menit' : 'Rata-rata RMS Harian' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ !empty($day) ? 'Agregasi RMS per 10 menit pada tanggal terpilih' : 'Tren rata-rata RMS selama periode' }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Preview</span>
                </div>
                <div class="mt-4 h-64">
                    <canvas id="monthlyRmsTrendChart"></canvas>
                </div>
            </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Daftar Abnormal Bulanan</h3>
                <a href="{{ route('monthly-report.export', $reportQuery) }}"
                   class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Export CSV</a>
            </div>
            <div class="mt-4 overflow-x-auto rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-emerald-700 text-white">
                        <tr class="text-white">
                            <th class="px-4 py-3 text-left font-semibold rounded-tl-lg">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Mesin</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">RMS</th>
                            <th class="px-4 py-3 text-left font-semibold rounded-tr-lg">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(($abnormalList ?? []) as $item)
                            @php
                                $status = strtoupper($item->condition_status ?? 'UNKNOWN');
                                $badgeClass = in_array($status, ['CRITICAL', 'FAULT', 'ANOMALY'], true)
                                    ? 'bg-red-50 text-red-700'
                                    : 'bg-yellow-50 text-yellow-700';
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->machine?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full {{ $badgeClass }} text-xs font-semibold">{{ $status }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($item->rms ?? 0, 2) }} mm/s</td>
                                <td class="px-4 py-3 text-gray-600">-</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data abnormal pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="font-bold text-gray-900">Rekap per Mesin</h3>
            <div class="mt-4 overflow-x-auto rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-emerald-700 text-white">
                        <tr class="text-white">
                            <th class="px-4 py-3 text-left font-semibold rounded-tl-lg">Mesin</th>
                            <th class="px-4 py-3 text-left font-semibold">Total</th>
                            <th class="px-4 py-3 text-left font-semibold">Normal</th>
                            <th class="px-4 py-3 text-left font-semibold">Warning</th>
                            <th class="px-4 py-3 text-left font-semibold">Critical</th>
                            <th class="px-4 py-3 text-left font-semibold rounded-tr-lg">RMS Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(($machineSummary ?? []) as $row)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $row['machine']?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($row['normal'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($row['warning'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($row['critical'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($row['avg_rms'] ?? 0, 2) }} mm/s</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('monthlyAbnormalChart');
            if (ctx) {
                const weeklyValues = {!! json_encode(($weeklyCounts ?? collect([0,0,0,0,0]))->values()) !!};
                const maxWeekly = Math.max(...weeklyValues);
                const maxIndex = weeklyValues.indexOf(maxWeekly);
                const summaryTarget = document.getElementById('abnormalWeeklySummary');
                if (summaryTarget && maxWeekly > 0) {
                    summaryTarget.textContent = `Minggu ke-${maxIndex + 1} paling tinggi: ${maxWeekly} kejadian.`;
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                        datasets: [{
                            label: 'Jumlah Abnormal',
                            data: weeklyValues,
                            backgroundColor: ['#FBBF24', '#F97316', '#F59E0B', '#FCD34D'],
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.parsed.y} kejadian`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f3f4f6' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    },
                    plugins: [{
                        id: 'valueLabels',
                        afterDatasetsDraw(chart) {
                            const { ctx } = chart;
                            ctx.save();
                            ctx.fillStyle = '#111827';
                            ctx.font = 'bold 13px Arial';
                            chart.getDatasetMeta(0).data.forEach((bar, index) => {
                                const value = weeklyValues[index] ?? 0;
                                const text = String(value);
                                const textWidth = ctx.measureText(text).width;
                                const yPos = Math.max(bar.y - 10, chart.scales.y.top + 16);
                                ctx.fillText(text, bar.x - textWidth / 2, yPos);
                            });
                            ctx.restore();
                        }
                    }]
                });
            }

            const rmsCtx = document.getElementById('monthlyRmsTrendChart');
            if (rmsCtx) {
                const isDaily = {!! json_encode(!empty($day)) !!};
                const labels = isDaily
                    ? {!! json_encode(($rawTrend ?? collect())->pluck('label')->toArray()) !!}
                    : {!! json_encode(($dailyTrend ?? collect())->pluck('label')->toArray()) !!};
                const values = isDaily
                    ? {!! json_encode(($rawTrend ?? collect())->pluck('rms')->toArray()) !!}
                    : {!! json_encode(($dailyTrend ?? collect())->pluck('avg_rms')->toArray()) !!};

                new Chart(rmsCtx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'RMS (mm/s)',
                            data: values,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f3f4f6' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    },
                    plugins: [{
                        id: 'rmsValueLabels',
                        afterDatasetsDraw(chart) {
                            const { ctx } = chart;
                            ctx.save();
                            ctx.fillStyle = '#111827';
                            ctx.font = 'bold 10px Arial';
                            chart.getDatasetMeta(0).data.forEach((point, index) => {
                                const value = values[index];
                                if (value === null || value === undefined) return;
                                const text = Number(value).toFixed(isDaily ? 3 : 2);
                                const textWidth = ctx.measureText(text).width;
                                const yPos = Math.max(point.y - 8, chart.scales.y.top + 12);
                                ctx.fillText(text, point.x - textWidth / 2, yPos);
                            });
                            ctx.restore();
                        }
                    }]
                });
            }

            const downloadBtn = document.getElementById('downloadPdfBtn');
            if (downloadBtn) {
                const captureChart = (canvas, targetW = 720, targetH = 260) => {
                    if (!canvas) return '';
                    const temp = document.createElement('canvas');
                    temp.width = targetW;
                    temp.height = targetH;
                    const ctx2d = temp.getContext('2d');
                    ctx2d.fillStyle = '#ffffff';
                    ctx2d.fillRect(0, 0, targetW, targetH);
                    ctx2d.drawImage(canvas, 0, 0, targetW, targetH);
                    return temp.toDataURL('image/jpeg', 0.8);
                };

                downloadBtn.addEventListener('click', async () => {
                    const abnormalCanvas = document.getElementById('monthlyAbnormalChart');
                    const rmsCanvas = document.getElementById('monthlyRmsTrendChart');

                    document.getElementById('chartAbnormalInput').value = captureChart(abnormalCanvas);
                    document.getElementById('chartRmsInput').value = captureChart(rmsCanvas);

                    const pdfForm = document.getElementById('monthlyReportPdfForm');
                    if (!pdfForm) return;

                    // 1) Preview in new tab
                    pdfForm.submit();

                    // 2) Auto-download in background (use hidden iframe)
                    const iframeName = 'pdfDownloadFrame';
                    let iframe = document.getElementById(iframeName);
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = iframeName;
                        iframe.name = iframeName;
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                    }

                    const downloadForm = pdfForm.cloneNode(true);
                    downloadForm.style.display = 'none';
                    downloadForm.target = iframeName;

                    const downloadInput = document.createElement('input');
                    downloadInput.type = 'hidden';
                    downloadInput.name = 'download';
                    downloadInput.value = '1';
                    downloadForm.appendChild(downloadInput);

                    document.body.appendChild(downloadForm);
                    downloadForm.submit();
                    downloadForm.remove();
                });
            }
        </script>
    @endpush
</x-app-layout>
