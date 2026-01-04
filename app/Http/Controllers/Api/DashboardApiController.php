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

    /**
     * Get sensor data for a specific machine
     */
    public function getMachineSensorData($id)
    {
        try {
            $machine = Machine::with('latestAnalysis')->findOrFail($id);

            // Get latest 20 sensor readings
            $sensorData = RawSample::where('machine_id', $id)
                ->latest()
                ->limit(20)
                ->get()
                ->map(function($sample) {
                    return [
                        'id' => $sample->id,
                        'timestamp' => $sample->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $sample->created_at->diffForHumans(),
                        'acceleration_x' => round($sample->ax_g ?? 0, 4),
                        'acceleration_y' => round($sample->ay_g ?? 0, 4),
                        'acceleration_z' => round($sample->az_g ?? 0, 4),
                    ];
                });

            // Get latest analysis
            $latestAnalysis = $machine->latestAnalysis;

            return response()->json([
                'success' => true,
                'machine' => [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'location' => $machine->location,
                    'type' => $machine->type,
                    'status' => $latestAnalysis ? $latestAnalysis->condition_status : 'UNKNOWN',
                    'rms' => $latestAnalysis ? round($latestAnalysis->rms, 4) : 0,
                    'peak_amp' => $latestAnalysis ? round($latestAnalysis->peak_amp, 4) : 0,
                    'dominant_freq' => $latestAnalysis ? round($latestAnalysis->dominant_freq_hz, 2) : 0,
                    'last_check' => $latestAnalysis ? $latestAnalysis->created_at->diffForHumans() : 'Never',
                ],
                'sensor_data' => $sensorData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get historical sensor data for a specific machine, date, and time range
     */
    public function getHistoricalData($id, Request $request)
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            $hours = $request->input('hours', 24); // Default to 24 hours

            // Parse the date and calculate trailing time window
            $selectedDayStart = \Carbon\Carbon::parse($date)->startOfDay();
            $selectedDayEnd = \Carbon\Carbon::parse($date)->endOfDay();

            // End of window: end of selected day, but not beyond now if same day
            $endDate = $selectedDayEnd;
            if ($selectedDayEnd->isToday()) {
                $endDate = now();
            }

            // Start of window: trailing $hours before end, but not before start of day
            $startDate = $endDate->copy()->subHours($hours);
            if ($startDate->lessThan($selectedDayStart)) {
                $startDate = $selectedDayStart;
            }

            // Get sensor data within the date range
            $sensorData = RawSample::where('machine_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($sample) {
                    return [
                        'id' => $sample->id,
                        'timestamp' => $sample->created_at->toIso8601String(),
                        'acceleration_x' => round($sample->ax_g ?? 0, 4),
                        'acceleration_y' => round($sample->ay_g ?? 0, 4),
                        'acceleration_z' => round($sample->az_g ?? 0, 4),
                    ];
                });

            // If we have too many data points, sample them (e.g., max 500 points)
            if ($sensorData->count() > 500) {
                $step = ceil($sensorData->count() / 500);
                $sensorData = $sensorData->filter(function($item, $key) use ($step) {
                    return $key % $step === 0;
                })->values();
            }

            return response()->json([
                'success' => true,
                'sensor_data' => $sensorData,
                'date_range' => [
                    'start' => $startDate->format('Y-m-d H:i:s'),
                    'end' => $endDate->format('Y-m-d H:i:s'),
                ],
                'total_points' => $sensorData->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get historical trend data for Data Grafik page
     */
    public function getHistoricalTrend($id, Request $request)
    {
        try {
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');

            if (!$dateFrom || !$dateTo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date range required'
                ], 400);
            }

            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay();

            // Validate date range
            if ($startDate > $endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date range'
                ], 400);
            }

            // Get machine info
            $machine = Machine::findOrFail($id);

            // Get analysis results (for RMS trend)
            $analysisData = AnalysisResult::where('machine_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($analysis) {
                    return [
                        'timestamp' => $analysis->created_at->format('Y-m-d H:i:s'),
                        'rms_value' => round($analysis->rms ?? 0, 4),
                        'peak_amplitude' => round($analysis->peak_amp ?? 0, 4),
                        'dominant_frequency' => round($analysis->dominant_freq ?? 0, 1),
                        'is_anomaly' => $analysis->condition_status === 'ANOMALY' ? 1 : 0,
                        'status' => $analysis->condition_status
                    ];
                });

            if ($analysisData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data available for the selected date range',
                    'data' => [],
                    'machine_name' => $machine->name
                ]);
            }

            // Calculate statistics
            $rmsValues = $analysisData->pluck('rms_value')->filter();
            $stats = [
                'min_rms' => $rmsValues->min(),
                'max_rms' => $rmsValues->max(),
                'avg_rms' => $rmsValues->avg(),
                'total_readings' => $analysisData->count(),
                'anomaly_count' => $analysisData->where('is_anomaly', 1)->count(),
                'normal_count' => $analysisData->where('is_anomaly', 0)->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $analysisData,
                'machine_name' => $machine->name,
                'machine_id' => $machine->id,
                'statistics' => $stats,
                'date_range' => [
                    'from' => $startDate->format('Y-m-d'),
                    'to' => $endDate->format('Y-m-d'),
                    'total_days' => $startDate->diffInDays($endDate) + 1
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
