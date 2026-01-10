<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnalysisResult;
use Carbon\Carbon;

class GrafikRmsController extends Controller
{
    public function index(Request $request)
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

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
