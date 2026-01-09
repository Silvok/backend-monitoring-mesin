<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawSample;
use Carbon\Carbon;

class GrafikController extends Controller
{
    public function getRmsData(Request $request)
    {
        $machineId = $request->input('machine_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$machineId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak lengkap',
            ], 400);
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $results = AnalysisResult::where('machine_id', $machineId)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $labels = $results->pluck('created_at')->map(function($date) {
            return Carbon::parse($date)->format('d-m-Y H:i');
        })->toArray();

        $values = $results->pluck('rms')->toArray();

        if (count($labels) === 0 || count($values) === 0) {
            \Log::debug('Grafik API: Data tidak ditemukan', [
                'machine_id' => $machineId,
                'start' => $start,
                'end' => $end,
                'query_count' => $results->count(),
                'query_sql' => $results->toSql() ?? null,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'debug' => [
                    'machine_id' => $machineId,
                    'start' => $start,
                    'end' => $end,
                ]
            ], 200);
        }

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
