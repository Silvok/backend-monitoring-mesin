<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\RawSample;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardApiController extends Controller
{
    public function getDashboardData()
    {
        // Cache dashboard data for 30 seconds
        return Cache::remember('api_dashboard_data', 30, function () {
            $totalMachines = Machine::count();
            $totalSamples = RawSample::count();
            $totalAnalysis = AnalysisResult::count();
            $anomalyCount = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'FAULT', 'CRITICAL'])->count();
            $normalCount = AnalysisResult::where('condition_status', 'NORMAL')->count();

            $rmsData = AnalysisResult::where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'asc')
                ->select('rms', 'created_at')
                ->get()
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
                'timestamp' => now()->format('l, d-m-Y H:i')
            ]);
        });
    }

    /**
     * Get all machines with their latest status
     */
    public function getMachineStatus()
    {
        try {
            // Cache for 15 seconds
            $machines = Cache::remember('api_machine_status', 15, function () {
                return Machine::with('latestAnalysis')
                    ->get()
                    ->map(function($machine) {
                        $latest = $machine->latestAnalysis;
                        $rmsValue = $latest ? $latest->rms : 0;

                        // Calculate status based on ISO 10816-3 thresholds (mm/s)
                        $status = 'UNKNOWN';
                        if ($latest) {
                            if ($rmsValue < 2.8) {
                                $status = 'NORMAL';      // Zone A: Good
                            } elseif ($rmsValue < 7.1) {
                                $status = 'WARNING';     // Zone B: Acceptable
                            } else {
                                $status = 'ANOMALY';     // Zone C/D: Unsatisfactory
                            }
                        }

                        return [
                            'id' => $machine->id,
                            'name' => $machine->name,
                            'location' => $machine->location,
                            'status' => $status,
                            'rms' => $rmsValue,
                            'peak_amp' => $latest ? $latest->peak_amp : 0,
                            'dominant_freq' => $latest ? $latest->dominant_freq_hz : 0,
                            'last_check' => $latest ? $latest->created_at->diffForHumans() : 'Never',
                            'last_check_time' => $latest ? $latest->created_at->format('l, d-m-Y H:i') : null,
                        ];
                    });
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
            // Cache for 15 seconds
            $topMachines = Cache::remember('api_top_machines_risk', 15, function () {
                $query = AnalysisResult::with('machine')
                    ->where('created_at', '>=', now()->subDay())
                    ->orderBy('rms', 'desc')
                    ->limit(5)
                    ->get();

                // Fallback: if no data in last 24h, take latest 5 overall
                if ($query->isEmpty()) {
                    $query = AnalysisResult::with('machine')
                        ->orderBy('created_at', 'desc')
                        ->orderBy('rms', 'desc')
                        ->limit(5)
                        ->get();
                }

                return $query->map(function($analysis) {
                    // ISO 10816-3 Thresholds (mm/s) for Medium Machines
                    $severity = 'low';
                    if ($analysis->rms >= 7.1) {
                        $severity = 'critical';  // Danger zone
                    } elseif ($analysis->rms >= 2.8) {
                        $severity = 'high';      // Warning zone
                    }

                    return [
                        'machine_id' => $analysis->machine_id,
                        'machine_name' => optional($analysis->machine)->name,
                        'location' => optional($analysis->machine)->location,
                        'rms' => $analysis->rms,
                        'severity' => $severity,
                        'status' => $analysis->condition_status,
                        'timestamp' => $analysis->created_at->format('l, d-m-Y H:i'),
                        'time_ago' => $analysis->created_at->diffForHumans(),
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'machines' => $topMachines,
                'top_machines' => $topMachines,
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

            // Today's window
            $todayStart = now()->startOfDay();
            $todayEnd = now();

            // Aggregate today's readings and analysis summary
            $totalReadingsToday = RawSample::where('machine_id', $id)
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();

            $anomalyStatuses = ['ANOMALY', 'WARNING', 'FAULT', 'CRITICAL'];

            $todayAnalysisQuery = AnalysisResult::where('machine_id', $id)
                ->whereBetween('created_at', [$todayStart, $todayEnd]);

            $totalAnalysisToday = (clone $todayAnalysisQuery)->count();
            $normalCountToday = (clone $todayAnalysisQuery)->where('condition_status', 'NORMAL')->count();
            $anomalyCountToday = (clone $todayAnalysisQuery)->whereIn('condition_status', $anomalyStatuses)->count();

            $lastAnomaly = AnalysisResult::where('machine_id', $id)
                ->whereIn('condition_status', $anomalyStatuses)
                ->orderBy('created_at', 'desc')
                ->first();

            $uptimePercent = $totalAnalysisToday > 0
                ? round(($normalCountToday / $totalAnalysisToday) * 100, 1)
                : 0.0;

            $normalPercent = $totalAnalysisToday > 0
                ? round(($normalCountToday / $totalAnalysisToday) * 100, 1)
                : 0.0;

            // Get latest 20 sensor readings
            $sensorData = RawSample::where('machine_id', $id)
                ->latest()
                ->limit(20)
                ->get()
                ->map(function($sample) {
                    return [
                        'id' => $sample->id,
                        'timestamp' => $sample->created_at->format('l, d-m-Y H:i'),
                        'time_ago' => $sample->created_at->diffForHumans(),
                        'acceleration_x' => round($sample->ax_g ?? 0, 4),
                        'acceleration_y' => round($sample->ay_g ?? 0, 4),
                        'acceleration_z' => round($sample->az_g ?? 0, 4),
                        'temperature' => $sample->temperature_c ? round($sample->temperature_c, 1) : null,
                    ];
                });

            // Get latest analysis
            $latestAnalysis = $machine->latestAnalysis;

            // Tentukan status berdasarkan RMS (ISO 10816-3 Thresholds - mm/s)
            $rmsValue = $latestAnalysis ? round($latestAnalysis->rms, 4) : 0;
            if ($latestAnalysis) {
                if ($rmsValue < 2.8) {
                    $status = 'NORMAL';      // Zone A: Good
                } elseif ($rmsValue < 7.1) {
                    $status = 'WASPADA';     // Zone B: Acceptable
                } else {
                    $status = 'ANOMALI';     // Zone C/D: Unsatisfactory/Danger
                }
            } else {
                $status = 'UNKNOWN';
            }

            return response()->json([
                'success' => true,
                'machine' => [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'location' => $machine->location,
                    'type' => $machine->type,
                    'status' => $status,
                    'rms' => $rmsValue,
                    'peak_amp' => $latestAnalysis ? round($latestAnalysis->peak_amp, 4) : 0,
                    'dominant_freq' => $latestAnalysis ? round($latestAnalysis->dominant_freq_hz, 2) : 0,
                    'last_check' => $latestAnalysis ? $latestAnalysis->created_at->diffForHumans() : 'Never',
                ],
                'sensor_data' => $sensorData,
                'summary' => [
                    'total_readings' => $totalReadingsToday,
                    'total_analysis' => $totalAnalysisToday,
                    'normal_count' => $normalCountToday,
                    'anomaly_count' => $anomalyCountToday,
                    'uptime_percent' => $uptimePercent,
                    'normal_percent' => $normalPercent,
                    'last_anomaly' => $lastAnomaly ? $lastAnomaly->created_at->toIso8601String() : null,
                ],
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
                    'start' => $startDate->format('l, d-m-Y H:i'),
                    'end' => $endDate->format('l, d-m-Y H:i'),
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
                        'timestamp' => $analysis->created_at->format('l, d-m-Y H:i'),
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

    /**
     * Get analysis data for analisis page
     */
    public function getAnalysisData(Request $request)
    {
        try {
            $machineFilter = $request->input('machine_id', 'all');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            if (!$dateFrom || !$dateTo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date range required'
                ], 400);
            }

            $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateTo)->endOfDay();

            // Get machines to analyze
            $machines = $machineFilter === 'all'
                ? Machine::all()
                : Machine::where('id', $machineFilter)->get();

            if ($machines->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No machines found',
                    'debug' => [
                        'machineFilter' => $machineFilter,
                        'totalMachines' => Machine::count()
                    ]
                ], 404);
            }

            $healthScores = [];
            $comparativeData = [
                'machine_names' => [],
                'avg_rms' => [],
                'anomaly_rates' => []
            ];
            $statisticalSummary = [];

            foreach ($machines as $machine) {
                // Get analysis results for this machine in date range
                $analysisResults = AnalysisResult::where('machine_id', $machine->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                if ($analysisResults->isEmpty()) {
                    continue;
                }

                $rmsValues = $analysisResults->pluck('rms')->filter()->map(function($val) {
                    return (float) $val;
                })->values();

                $anomalyCount = $analysisResults->filter(function($result) {
                    return in_array(strtolower($result->condition_status), ['anomaly', 'warning', 'critical']);
                })->count();
                $totalCount = $analysisResults->count();
                $anomalyRate = $totalCount > 0 ? ($anomalyCount / $totalCount) * 100 : 0;

                // Calculate statistics
                $avgRms = $rmsValues->avg();
                $minRms = $rmsValues->min();
                $maxRms = $rmsValues->max();

                // Standard deviation
                $mean = $avgRms;
                $variance = $rmsValues->map(function($val) use ($mean) {
                    return pow($val - $mean, 2);
                })->avg();
                $stdDev = sqrt($variance);

                // Calculate health score (0-100)
                // Lower RMS, lower anomaly rate, lower std dev = higher score
                $rmsScore = max(0, 100 - ($avgRms * 50)); // Normalize RMS
                $anomalyScore = max(0, 100 - ($anomalyRate * 2)); // Penalize anomalies
                $stabilityScore = max(0, 100 - ($stdDev * 100)); // Penalize instability

                $healthScore = round(($rmsScore + $anomalyScore + $stabilityScore) / 3);

                // Health Scores
                $healthScores[] = [
                    'machine_id' => $machine->id,
                    'machine_name' => $machine->name,
                    'health_score' => $healthScore,
                    'avg_rms' => round($avgRms, 4),
                    'anomaly_count' => $anomalyCount,
                    'anomaly_rate' => round($anomalyRate, 2)
                ];

                // Comparative Data
                $comparativeData['machine_names'][] = $machine->name;
                $comparativeData['avg_rms'][] = round($avgRms, 4);
                $comparativeData['anomaly_rates'][] = round($anomalyRate, 2);

                // Statistical Summary
                $statisticalSummary[] = [
                    'machine_id' => $machine->id,
                    'machine_name' => $machine->name,
                    'total_data' => $totalCount,
                    'rms_min' => round($minRms, 4),
                    'rms_max' => round($maxRms, 4),
                    'rms_avg' => round($avgRms, 4),
                    'std_dev' => round($stdDev, 4),
                    'anomaly_count' => $anomalyCount,
                    'anomaly_rate' => round($anomalyRate, 2)
                ];
            }

            // Sort health scores by score descending
            usort($healthScores, function($a, $b) {
                return $b['health_score'] <=> $a['health_score'];
            });

            return response()->json([
                'success' => true,
                'health_scores' => $healthScores,
                'comparative_data' => $comparativeData,
                'statistical_summary' => $statisticalSummary,
                'date_range' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }}
