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

    /**
     * Get all machines with their latest status
     */
    public function getMachineStatus()
    {
        try {
            $machines = Machine::with('latestAnalysis')
                ->get()
                ->map(function($machine) {
                    $latest = $machine->latestAnalysis;
                    return [
                        'id' => $machine->id,
                        'name' => $machine->name,
                        'location' => $machine->location,
                        'status' => $latest ? $latest->condition_status : 'UNKNOWN',
                        'rms' => $latest ? $latest->rms : 0,
                        'peak_amp' => $latest ? $latest->peak_amp : 0,
                        'dominant_freq' => $latest ? $latest->dominant_freq_hz : 0,
                        'last_check' => $latest ? $latest->created_at->diffForHumans() : 'Never',
                        'last_check_time' => $latest ? $latest->created_at->format('Y-m-d H:i:s') : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'machines' => $machines,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top machines by risk (highest RMS values)
     */
    public function getTopMachinesByRisk()
    {
        try {
            $topMachines = AnalysisResult::with('machine')
                ->where('created_at', '>=', now()->subDay())
                ->orderBy('rms', 'desc')
                ->limit(5)
                ->get()
                ->map(function($analysis) {
                    $severity = 'low';
                    if ($analysis->rms >= 2.0) {
                        $severity = 'critical';
                    } elseif ($analysis->rms >= 1.5) {
                        $severity = 'high';
                    } elseif ($analysis->rms >= 1.0) {
                        $severity = 'medium';
                    }

                    return [
                        'machine_id' => $analysis->machine_id,
                        'machine_name' => $analysis->machine->name,
                        'location' => $analysis->machine->location,
                        'rms' => $analysis->rms,
                        'severity' => $severity,
                        'status' => $analysis->condition_status,
                        'timestamp' => $analysis->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $analysis->created_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'machines' => $topMachines,
                'total' => $topMachines->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
