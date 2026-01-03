<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\RawSample;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function getDashboardData()
    {
        // Get statistics
        $totalMachines = Machine::count();
        $totalSamples = RawSample::count();
        $totalAnalysis = AnalysisResult::count();

        // Calculate anomaly count
        $anomalyCount = AnalysisResult::where('condition_status', 'ANOMALY')->count();
        $normalCount = AnalysisResult::where('condition_status', 'NORMAL')->count();

        // Get RMS data for last 24 hours for chart
        $rmsData = AnalysisResult::where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->get(['rms', 'created_at'])
            ->map(function($item) {
                return [
                    'time' => $item->created_at->format('H:i'),
                    'value' => round($item->rms, 4)
                ];
            });

        return response()->json([
            'success' => true,
            'totalMachines' => $totalMachines,
            'totalSamples' => $totalSamples,
            'totalAnalysis' => $totalAnalysis,
            'anomalyCount' => $anomalyCount,
            'normalCount' => $normalCount,
            'rmsData' => $rmsData,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
