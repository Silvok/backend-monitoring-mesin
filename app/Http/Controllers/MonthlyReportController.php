<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Machine;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

        $pdf = Pdf::loadView('pages.monthly-report-print', $payload)
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ])
            ->setPaper('a4', 'portrait');

        $filename = 'laporan_bulanan_' . ($payload['month'] ?? now()->format('Y-m')) . '.pdf';
        $output = $pdf->output();

        return response()->make($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($output),
        ]);
    }

    private function buildReportData(Request $request): array
    {
        $month = $request->input('month');
        $machineId = $request->input('machine_id');

        $start = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $query = AnalysisResult::with('machine')
            ->whereBetween('created_at', [$start, $end]);

        if (!empty($machineId) && $machineId !== 'all') {
            $query->where('machine_id', $machineId);
        }

        $results = $query->get();

        $total = $results->count();
        $normal = $results->where('condition_status', 'NORMAL')->count();
        $warning = $results->where('condition_status', 'WARNING')->count();
        $critical = $results->whereIn('condition_status', ['CRITICAL', 'FAULT', 'ANOMALY'])->count();

        $abnormal = $results->whereIn('condition_status', ['WARNING', 'CRITICAL', 'FAULT', 'ANOMALY']);

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

        $dailyTrend = $results
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function ($items, $date) {
                return [
                    'date' => Carbon::parse($date)->format('d M'),
                    'avg_rms' => round($items->avg('rms'), 2),
                ];
            })
            ->sortKeys()
            ->values();

        $machines = Machine::orderBy('name')->get();

        return [
            'month' => $start->format('Y-m'),
            'machines' => $machines,
            'selectedMachine' => $machineId ?? 'all',
            'summary' => [
                'total' => $total,
                'normal' => $normal,
                'warning' => $warning,
                'critical' => $critical,
            ],
            'weeklyCounts' => $weeklyCounts,
            'abnormalList' => $abnormalList,
            'machineSummary' => $machineSummary,
            'dailyTrend' => $dailyTrend,
        ];
    }
}
