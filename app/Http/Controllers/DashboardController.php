<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\RawSample;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalMachines = Machine::count();
        $totalSamples = RawSample::count();
        $totalAnalysis = AnalysisResult::count();

        // Get machine with latest analysis
        $machine = Machine::with(['latestAnalysis', 'rawSamples' => function($query) {
            $query->latest()->limit(10);
        }])->first();

        // Get recent analysis results
        $recentAnalysis = AnalysisResult::with('machine')
            ->latest()
            ->limit(5)
            ->get();

        // Get latest sensor readings
        $latestSensorData = RawSample::with('machine')
            ->latest()
            ->limit(10)
            ->get();

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

        // Prepare chart data
        $rmsChartData = [
            'labels' => $rmsData->pluck('time')->toArray(),
            'values' => $rmsData->pluck('value')->toArray()
        ];

        return view('pages.dashboard', compact(
            'totalMachines',
            'totalSamples',
            'totalAnalysis',
            'machine',
            'recentAnalysis',
            'latestSensorData',
            'anomalyCount',
            'normalCount',
            'rmsChartData'
        ));
    }

    public function realTimeSensor()
    {
        // Get all machines for dropdown
        $machines = Machine::with('latestAnalysis')
            ->orderBy('name')
            ->get();

        return view('pages.real-time-sensor', compact('machines'));
    }
}
