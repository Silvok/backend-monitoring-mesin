<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;

class AnalisisController extends Controller
{
    public function index()
    {
        // Ambil semua mesin untuk dropdown
        $machines = Machine::orderBy('name')->get();
        // Ambil tanggal awal & akhir dari tabel analysis_results
        $latestDate = AnalysisResult::orderBy('created_at', 'desc')->value('created_at');
        $earliestDate = AnalysisResult::orderBy('created_at', 'asc')->value('created_at');

        // Ambil filter dari request
        $machineId = request('machine_id');
        $startDate = request('start_date');
        $endDate = request('end_date');
        $conditionStatus = request('condition_status');

        $query = AnalysisResult::query();
        if ($machineId) {
            $query->where('machine_id', $machineId);
        }
        if ($conditionStatus !== null && $conditionStatus !== '') {
            $query->whereRaw('UPPER(condition_status) = ?', [strtoupper($conditionStatus)]);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        } else {
            $query->where('created_at', '>=', now()->subHours(24));
        }
        $rawResults = $query->orderBy('created_at', 'asc')->with('machine')->get();

        // Agregasi sesuai interval yang dipilih user (default 3 menit)
        $interval = (int) request('aggregation_interval', 3);
        $allowedIntervals = [1, 3, 5, 10, 15];
        if (!in_array($interval, $allowedIntervals)) {
            $interval = 3;
        }
        $grouped = $rawResults->groupBy(function($item) use ($interval) {
            $minute = floor($item->created_at->minute / $interval) * $interval;
            return $item->created_at->format('d-m H:') . str_pad($minute, 2, '0', STR_PAD_LEFT);
        });
        $rmsData = $grouped->map(function($items, $label) {
            $first = $items->first();
            return [
                'time' => $label,
                'value' => round($items->avg('rms'), 4),
                'full_time' => $first ? $first->created_at->format('Y-m-d H:i:s') : null,
                'machine' => $first && $first->machine ? $first->machine->name : null,
                'status' => $first ? $first->condition_status : null
            ];
        })->values();

        $rmsChartData = [
            'labels' => $rmsData->pluck('time')->toArray(),
            'values' => $rmsData->pluck('value')->toArray(),
            'full_times' => $rmsData->pluck('full_time')->toArray(),
            'machines' => $rmsData->pluck('machine')->toArray(),
            'statuses' => $rmsData->pluck('status')->toArray(),
        ];

        // Data summary lama (untuk card summary jika masih dipakai)
        $totalMachines = Machine::count();
        $rawCounts = AnalysisResult::select('condition_status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('condition_status')
            ->pluck('count', 'condition_status');
        $categories = [
            'Normal' => 0,
            'Warning' => 0,
            'Alert' => 0,
        ];
        foreach ($rawCounts as $status => $count) {
            $key = match (strtolower($status)) {
                'normal' => 'Normal',
                'warning', 'peringatan' => 'Warning',
                'alert', 'anomaly', 'anomali', 'critical', 'kritis', 'fault', 'gangguan' => 'Alert',
                default => null
            };
            if ($key) $categories[$key] += $count;
        }
        $lastUpdate = AnalysisResult::latest()->first()?->updated_at;

        return view('pages.analisis', compact(
            'machines',
            'earliestDate',
            'latestDate',
            'rmsChartData',
            'rawResults',
            'totalMachines',
            'categories',
            'lastUpdate'
        ));
    }
}
