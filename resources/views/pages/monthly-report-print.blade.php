<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
        h1 { margin: 0 0 4px; }
        .muted { color: #6b7280; font-size: 12px; }
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
        .card h3 { margin: 0 0 6px; font-size: 13px; color: #374151; }
        .card .value { font-size: 20px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; font-size: 12px; text-align: left; }
        th { background: #f3f4f6; }
        .section-title { margin-top: 24px; font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Laporan Bulanan</h1>
    <div class="muted">Periode: {{ $day ?? ($month ?? '-') }}</div>

    <table style="width:100%; border-collapse: separate; border-spacing: 8px; margin-top: 12px;">
        <tr>
            <td style="width:25%;">
                <div class="card">
                    <h3>Total Data</h3>
                    <div class="value">{{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width:25%;">
                <div class="card">
                    <h3>Normal</h3>
                    <div class="value">{{ number_format($summary['normal'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width:25%;">
                <div class="card">
                    <h3>Warning</h3>
                    <div class="value">{{ number_format($summary['warning'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width:25%;">
                <div class="card">
                    <h3>Critical</h3>
                    <div class="value">{{ number_format($summary['critical'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Ringkasan Eksekutif</div>
    <div class="card" style="margin-top: 8px;">
        <div style="font-size: 12px; color: #374151;">
            Pada periode ini, sistem mencatat total <strong>{{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</strong> data
            dengan <strong>{{ number_format($summary['warning'] ?? 0, 0, ',', '.') }}</strong> warning dan
            <strong>{{ number_format($summary['critical'] ?? 0, 0, ',', '.') }}</strong> critical.
            Secara umum kondisi mesin
            <strong>{{ ($totalAbnormal ?? 0) > 0 ? 'terdapat beberapa abnormal' : 'relatif stabil' }}</strong>.
        </div>
    </div>

    <div class="section-title">Grafik Abnormal Mingguan</div>
    <div class="muted">Total abnormal bulan ini: {{ number_format($totalAbnormal ?? 0, 0, ',', '.') }} kejadian.</div>
    @if(!empty($chart_abnormal))
        <img src="{{ $chart_abnormal }}" alt="Grafik Abnormal" style="width: 100%; max-height: 260px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
    @else
        <div class="muted">Grafik tidak tersedia.</div>
    @endif

    <div class="section-title">{{ !empty($day) ? 'Grafik RMS per 10 Menit' : 'Grafik RMS Harian' }}</div>
    @if(!empty($chart_rms))
        <img src="{{ $chart_rms }}" alt="Grafik RMS" style="width: 100%; max-height: 260px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
    @else
        <div class="muted">Grafik tidak tersedia.</div>
    @endif

    <div class="section-title">Statistik RMS</div>
    <table style="width:100%; border-collapse: separate; border-spacing: 8px; margin-top: 8px;">
        <tr>
            <td style="width:25%;"><div class="card"><h3>Min</h3><div class="value">{{ number_format($rmsStats['min'] ?? 0, 2) }} mm/s</div></div></td>
            <td style="width:25%;"><div class="card"><h3>Max</h3><div class="value">{{ number_format($rmsStats['max'] ?? 0, 2) }} mm/s</div></div></td>
            <td style="width:25%;"><div class="card"><h3>Rata-rata</h3><div class="value">{{ number_format($rmsStats['avg'] ?? 0, 2) }} mm/s</div></div></td>
            <td style="width:25%;"><div class="card"><h3>Median</h3><div class="value">{{ number_format($rmsStats['median'] ?? 0, 2) }} mm/s</div></div></td>
        </tr>
    </table>

    <div class="section-title">Distribusi Status</div>
    <table>
        <thead>
            <tr>
                <th>Normal</th>
                <th>Warning</th>
                <th>Critical</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($statusDistribution['normal'] ?? 0, 1) }}%</td>
                <td>{{ number_format($statusDistribution['warning'] ?? 0, 1) }}%</td>
                <td>{{ number_format($statusDistribution['critical'] ?? 0, 1) }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Highlight Abnormal Terbesar</div>
    <div class="card" style="margin-top: 8px;">
        @if(!empty($topAbnormal))
            <div style="font-size: 12px; color: #374151;">
                {{ $topAbnormal->created_at?->format('Y-m-d H:i') }} —
                <strong>{{ number_format($topAbnormal->rms ?? 0, 2) }} mm/s</strong>
                ({{ $topAbnormal->machine?->name ?? '-' }})
            </div>
        @else
            <div class="muted">Belum ada data abnormal.</div>
        @endif
    </div>

    <div class="section-title">Parameter Pengukuran</div>
    <table>
        <thead>
            <tr>
                <th>Interval Sampling</th>
                <th>Band-pass</th>
                <th>Satuan</th>
                <th>Tanggal Data</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $measurementParams['sampling_interval'] ?? 1 }} menit</td>
                <td>{{ $measurementParams['band_pass'] ?? '10–500 Hz' }}</td>
                <td>{{ $measurementParams['unit'] ?? 'mm/s' }}</td>
                <td>{{ $measurementParams['period_label'] ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Catatan Tindakan / Rekomendasi</div>
    <ul style="font-size: 12px; color: #374151; margin-top: 8px;">
        <li>Periksa bearing jika RMS cenderung meningkat pada minggu yang sama.</li>
        <li>Cek alignment dan kekencangan belt pada jam dengan RMS tertinggi.</li>
        <li>Jadwalkan inspeksi ulang bila warning berulang lebih dari 3 kali.</li>
    </ul>

    <div class="section-title">Daftar Abnormal</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Mesin</th>
                <th>Status</th>
                <th>RMS (mm/s)</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($abnormalList ?? []) as $item)
                <tr>
                    <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->machine?->name ?? '-' }}</td>
                    <td>{{ strtoupper($item->condition_status ?? '-') }}</td>
                    <td>{{ number_format($item->rms ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada data abnormal.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Rekap Per Mesin</div>
    <table>
        <thead>
            <tr>
                <th>Mesin</th>
                <th>Total</th>
                <th>Normal</th>
                <th>Warning</th>
                <th>Critical</th>
                <th>RMS Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($machineSummary ?? []) as $row)
                <tr>
                    <td>{{ $row['machine']?->name ?? '-' }}</td>
                    <td>{{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($row['normal'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($row['warning'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($row['critical'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($row['avg_rms'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
