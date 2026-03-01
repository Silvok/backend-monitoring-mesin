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
    <div class="muted">Periode: {{ $month ?? '-' }}</div>

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

    <div class="section-title">Grafik Abnormal Mingguan</div>
    @if(!empty($chart_abnormal))
        <img src="{{ $chart_abnormal }}" alt="Grafik Abnormal" style="width: 100%; max-height: 260px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
    @else
        <div class="muted">Grafik tidak tersedia.</div>
    @endif

    <div class="section-title">Grafik RMS Harian</div>
    @if(!empty($chart_rms))
        <img src="{{ $chart_rms }}" alt="Grafik RMS" style="width: 100%; max-height: 260px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
    @else
        <div class="muted">Grafik tidak tersedia.</div>
    @endif

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
