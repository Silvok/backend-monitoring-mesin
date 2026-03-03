<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Machine;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.monthly-report', $this->buildReportData($request));
    }

    public function exportCsv(Request $request)
    {
        $month = $request->input('month');
        $machineId = $request->input('machine_id');

        $start = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $query = AnalysisResult::with('machine')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('condition_status', ['WARNING', 'CRITICAL', 'FAULT', 'ANOMALY']);

        if (!empty($machineId) && $machineId !== 'all') {
            $query->where('machine_id', $machineId);
        }

        $rows = $query->orderBy('created_at', 'desc')->get();

        $csv = "Tanggal,Mesin,Status,RMS (mm/s),Peak Amp,Frekuensi Dominan\n";
        foreach ($rows as $row) {
            $csv .= implode(',', [
                $row->created_at?->format('Y-m-d H:i:s'),
                $row->machine?->name ?? '-',
                strtoupper($row->condition_status ?? '-'),
                $row->rms ?? 0,
                $row->peak_amp ?? 0,
                $row->dominant_freq_hz ?? 0,
            ]) . "\n";
        }

        $filename = 'laporan_bulanan_' . $start->format('Y-m') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPdf(Request $request)
    {
        $payload = $this->buildReportData($request);
        $payload['chart_abnormal'] = $request->input('chart_abnormal');
        $payload['chart_rms'] = $request->input('chart_rms');

        if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
            \Barryvdh\Debugbar\Facade::disable();
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $pdf = Pdf::loadView('pages.monthly-report-print', $payload)
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ])
            ->setPaper('a4', 'portrait');

        $filenameSuffix = !empty($payload['day']) ? $payload['day'] : ($payload['month'] ?? now()->format('Y-m'));
        $filename = 'laporan_bulanan_' . $filenameSuffix . '.pdf';
        $output = $pdf->output();
        if (!str_starts_with($output, '%PDF')) {
            \Log::error('PDF output invalid', ['head' => substr($output, 0, 20)]);
        }

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, $output);

        $disposition = $request->boolean('download')
            ? 'attachment'
            : 'inline';

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            'Content-Length' => (string) strlen($output),
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    private function buildReportData(Request $request): array
    {
        $month = $request->input('month');
        $day = $request->input('day');
        $machineId = $request->input('machine_id');

        if (!empty($day)) {
            $start = Carbon::createFromFormat('Y-m-d', $day)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $day)->endOfDay();
        } else {
            $start = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();
            $end = (clone $start)->endOfMonth();
        }

        $query = AnalysisResult::with('machine');
        if (!empty($day)) {
            $query->whereDate('created_at', $day);
        } else {
            $query->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($machineId) && $machineId !== 'all') {
            $query->where('machine_id', $machineId);
        }

        $results = $query->get();

        $total = $results->count();
        $normal = $results->where('condition_status', 'NORMAL')->count();
        $warning = $results->where('condition_status', 'WARNING')->count();
        $critical = $results->whereIn('condition_status', ['CRITICAL', 'FAULT', 'ANOMALY'])->count();

        $abnormal = $results->whereIn('condition_status', ['WARNING', 'CRITICAL', 'FAULT', 'ANOMALY']);
        $totalAbnormal = $abnormal->count();

        $rmsValues = $results->pluck('rms')->filter(fn ($v) => $v !== null)->sort()->values();
        $rmsCount = $rmsValues->count();
        $medianRms = 0.0;
        if ($rmsCount > 0) {
            $mid = (int) floor(($rmsCount - 1) / 2);
            $medianRms = $rmsCount % 2 === 0
                ? round((($rmsValues[$mid] + $rmsValues[$mid + 1]) / 2), 2)
                : round($rmsValues[$mid], 2);
        }
        $minRms = $rmsCount > 0 ? round($rmsValues->first(), 2) : 0.0;
        $maxRms = $rmsCount > 0 ? round($rmsValues->last(), 2) : 0.0;
        $avgRms = $rmsCount > 0 ? round($rmsValues->avg(), 2) : 0.0;

        $weeklyCounts = collect([1, 2, 3, 4, 5])->map(function ($week) use ($abnormal) {
            return $abnormal->filter(function ($item) use ($week) {
                $day = $item->created_at->day;
                $weekOfMonth = (int) ceil($day / 7);
                return $weekOfMonth === $week;
            })->count();
        });

        $abnormalList = $abnormal
            ->sortByDesc('created_at')
            ->take(50)
            ->values();

        $topAbnormal = $abnormal
            ->sortByDesc('rms')
            ->first();

        $machineSummary = $results
            ->groupBy('machine_id')
            ->map(function ($items) {
                return [
                    'machine' => $items->first()->machine,
                    'total' => $items->count(),
                    'normal' => $items->where('condition_status', 'NORMAL')->count(),
                    'warning' => $items->where('condition_status', 'WARNING')->count(),
                    'critical' => $items->whereIn('condition_status', ['CRITICAL', 'FAULT', 'ANOMALY'])->count(),
                    'avg_rms' => round($items->avg('rms'), 2),
                ];
            })
            ->values();

        if (!empty($day)) {
            $rawTrend = $results
                ->groupBy(function ($item) {
                    $minuteBucket = floor((int) $item->created_at->format('i') / 10) * 10;
                    return $item->created_at->format('H:') . str_pad($minuteBucket, 2, '0', STR_PAD_LEFT);
                })
                ->map(function ($items, $label) {
                    return [
                        'label' => $label,
                        'rms' => round($items->avg('rms') ?? 0, 3),
                    ];
                })
                ->sortKeys()
                ->values();
        } else {
            $rawTrend = collect();
        }

        $topRmsPoints = !empty($day)
            ? $results
                ->groupBy(function ($item) {
                    $minuteBucket = floor((int) $item->created_at->format('i') / 10) * 10;
                    return $item->created_at->format('H:') . str_pad($minuteBucket, 2, '0', STR_PAD_LEFT);
                })
                ->map(function ($items, $label) {
                    return [
                        'label' => $label,
                        'avg_rms' => round($items->avg('rms') ?? 0, 3),
                    ];
                })
                ->sortByDesc('avg_rms')
                ->take(3)
                ->values()
            : $results
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                })
                ->map(function ($items, $date) {
                    return [
                        'label' => Carbon::parse($date)->format('d M'),
                        'avg_rms' => round($items->avg('rms') ?? 0, 2),
                    ];
                })
                ->sortByDesc('avg_rms')
                ->take(3)
                ->values();

        $dailyTrend = $results
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function ($items, $date) {
                return [
                    'label' => Carbon::parse($date)->format('d M'),
                    'avg_rms' => round($items->avg('rms'), 2),
                ];
            })
            ->sortKeys()
            ->values();

        $machines = Machine::orderBy('name')->get();

        $samplingInterval = Cache::get('sampling_interval_minutes', 1);
        $bandPass = Cache::get('band_pass_range', '10–500 Hz');

        return [
            'month' => $start->format('Y-m'),
            'day' => $day,
            'machines' => $machines,
            'selectedMachine' => $machineId ?? 'all',
            'summary' => [
                'total' => $total,
                'normal' => $normal,
                'warning' => $warning,
                'critical' => $critical,
            ],
            'totalAbnormal' => $totalAbnormal,
            'weeklyCounts' => $weeklyCounts,
            'abnormalList' => $abnormalList,
            'machineSummary' => $machineSummary,
            'dailyTrend' => $dailyTrend,
            'rawTrend' => $rawTrend,
            'topRmsPoints' => $topRmsPoints,
            'rmsStats' => [
                'min' => $minRms,
                'max' => $maxRms,
                'avg' => $avgRms,
                'median' => $medianRms,
            ],
            'statusDistribution' => [
                'normal' => $total > 0 ? round($normal / $total * 100, 1) : 0,
                'warning' => $total > 0 ? round($warning / $total * 100, 1) : 0,
                'critical' => $total > 0 ? round($critical / $total * 100, 1) : 0,
            ],
            'topAbnormal' => $topAbnormal,
            'measurementParams' => [
                'sampling_interval' => (int) $samplingInterval,
                'band_pass' => $bandPass,
                'unit' => 'mm/s',
                'period_label' => !empty($day) ? $day : ($start->format('Y-m')),
            ],
        ];
    }
}
