<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\RawSample;
use App\Models\AnalysisResult;
use App\Models\TemperatureReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Single cache key for all dashboard data - reduces cache lookups
        $dashboardData = Cache::remember('dashboard_all_data', 30, function () {
            // Run all counts in parallel using single query with conditional counts
            $stats = [
                'totalMachines' => Machine::count(),
                'totalSamples' => RawSample::count(),
                'totalAnalysis' => AnalysisResult::count(),
                'anomalyCount' => AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'FAULT', 'CRITICAL'])->count(),
                'normalCount' => AnalysisResult::where('condition_status', 'NORMAL')->count(),
            ];

            // Get machine status data (preloaded to avoid AJAX call)
            $machineStatus = Machine::with('latestAnalysis')
                ->get()
                ->map(function($machine) {
                    $latest = $machine->latestAnalysis;
                    $rmsValue = $latest ? $latest->rms : 0;

                    // Use per-machine threshold from database
                    $warningThreshold = (float) ($machine->threshold_warning ?? 1.8);
                    $criticalThreshold = (float) ($machine->threshold_critical ?? 4.5);

                    // Calculate status based on per-machine thresholds
                    $status = 'UNKNOWN';
                    if ($latest) {
                        if ($rmsValue < $warningThreshold) {
                            $status = 'NORMAL';
                        } elseif ($rmsValue < $criticalThreshold) {
                            $status = 'WARNING';
                        } else {
                            $status = 'ANOMALY';
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
                        'threshold_warning' => $warningThreshold,
                        'threshold_critical' => $criticalThreshold,
                    ];
                });

            // Get alerts data (preloaded to avoid AJAX call)
            $alerts = AnalysisResult::with('machine')
                ->where('condition_status', 'ANOMALY')
                ->where('created_at', '>=', now()->subDay())
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($analysis) {
                    // Use per-machine threshold from database
                    $machine = $analysis->machine;
                    $warningThreshold = (float) ($machine->threshold_warning ?? 1.8);
                    $criticalThreshold = (float) ($machine->threshold_critical ?? 4.5);

                    $severity = 'medium';
                    if ($analysis->rms >= $criticalThreshold) $severity = 'critical';  // Danger zone
                    elseif ($analysis->rms >= $warningThreshold) $severity = 'high';  // Warning zone

                    return [
                        'id' => $analysis->id,
                        'machine_id' => $analysis->machine_id,
                        'machine_name' => $machine->name ?? 'Unknown',
                        'location' => $machine->location ?? 'Unknown',
                        'status' => 'anomaly',
                        'severity' => $severity,
                        'rms' => $analysis->rms,
                        'time_ago' => $analysis->created_at->diffForHumans(),
                    ];
                });

            // Get top machines by risk (preloaded to avoid AJAX call)
            $topMachines = AnalysisResult::with('machine')
                ->where('created_at', '>=', now()->subDay())
                ->orderBy('rms', 'desc')
                ->limit(5)
                ->get()
                ->map(function($analysis) {
                    // Use per-machine threshold from database
                    $machine = $analysis->machine;
                    $warningThreshold = (float) ($machine->threshold_warning ?? 1.8);
                    $criticalThreshold = (float) ($machine->threshold_critical ?? 4.5);

                    $severity = 'low';
                    if ($analysis->rms >= $criticalThreshold) $severity = 'critical';  // Danger zone
                    elseif ($analysis->rms >= $warningThreshold) $severity = 'high';  // Warning zone

                    return [
                        'machine_id' => $analysis->machine_id,
                        'machine_name' => optional($machine)->name,
                        'location' => optional($machine)->location,
                        'rms' => $analysis->rms,
                        'severity' => $severity,
                        'status' => $analysis->condition_status,
                        'time_ago' => $analysis->created_at->diffForHumans(),
                    ];
                });

            // RMS chart data
            $rmsData = AnalysisResult::where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'asc')
                ->select('rms', 'created_at')
                ->limit(100) // Limit data points for faster rendering
                ->get()
                ->map(fn($item) => [
                    'time' => $item->created_at->format('H:i'),
                    'value' => round($item->rms, 4)
                ]);

            // Latest sensor & temperature data
            $latestSensorData = RawSample::with('machine')->latest()->limit(10)->get();
            $latestTemperatureData = TemperatureReading::with('machine')->latest('recorded_at')->limit(10)->get();

            return [
                'stats' => $stats,
                'machineStatus' => $machineStatus,
                'alerts' => $alerts,
                'topMachines' => $topMachines,
                'rmsChartData' => [
                    'labels' => $rmsData->pluck('time')->toArray(),
                    'values' => $rmsData->pluck('value')->toArray()
                ],
                'latestSensorData' => $latestSensorData,
                'latestTemperatureData' => $latestTemperatureData,
            ];
        });

        // Extract variables for view
        $totalMachines = $dashboardData['stats']['totalMachines'];
        $totalSamples = $dashboardData['stats']['totalSamples'];
        $totalAnalysis = $dashboardData['stats']['totalAnalysis'];
        $anomalyCount = $dashboardData['stats']['anomalyCount'];
        $normalCount = $dashboardData['stats']['normalCount'];
        $machine = Machine::first(); // Simple query for backward compatibility
        $recentAnalysis = collect([]);
        $latestSensorData = $dashboardData['latestSensorData'];
        $latestTemperatureData = $dashboardData['latestTemperatureData'];
        $rmsChartData = $dashboardData['rmsChartData'];

        // Pass preloaded data as JSON for JavaScript
        $preloadedData = [
            'machineStatus' => $dashboardData['machineStatus'],
            'alerts' => $dashboardData['alerts'],
            'topMachines' => $dashboardData['topMachines'],
        ];

        return view('pages.dashboard', compact(
            'totalMachines',
            'totalSamples',
            'totalAnalysis',
            'machine',
            'recentAnalysis',
            'latestSensorData',
            'latestTemperatureData',
            'anomalyCount',
            'normalCount',
            'rmsChartData',
            'preloadedData'
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

    public function dataGrafik(Request $request)
    {
        // Inisialisasi variabel $rawResults
        $rawResults = AnalysisResult::with('machine')->get();

        // Daftar mesin dengan status terburuk (FAULT/CRITICAL terbanyak)
        $worstMachines = $rawResults->whereIn('condition_status', ['FAULT', 'CRITICAL'])
            ->groupBy('machine_id')
            ->map(function ($items, $machineId) {
                return [
                    'machine_id' => $machineId,
                    'machine_name' => optional($items->first()->machine)->name,
                    'fault_count' => $items->count(),
                ];
            })
            ->sortByDesc('fault_count')
            ->values()
            ->take(5); // Top 5

        // Get all machines for dropdown
        $machines = Machine::orderBy('name')->get();
        // Get earliest and latest date from analysis_results
        $latestDate = AnalysisResult::orderBy('created_at', 'desc')->value('created_at');
        $earliestDate = AnalysisResult::orderBy('created_at', 'asc')->value('created_at');
        // Get last update (latest analysis result)
        $lastUpdate = AnalysisResult::orderBy('created_at', 'desc')->first()?->created_at;

        // Ambil filter dari request
        $machineId = $request->input('machine_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $conditionStatus = $request->input('condition_status');

        $query = AnalysisResult::query();
        if ($machineId) {
            $query->where('machine_id', $machineId);
        }
        if ($conditionStatus !== null && $conditionStatus !== '') {
            $query->whereRaw('UPPER(condition_status) = ?', [strtoupper($conditionStatus)]);
        }
        if ($startDate && $endDate) {
            // Filter berdasarkan range tanggal (00:00:00 - 23:59:59)
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        } else {
            // Default: 24 jam terakhir
            $query->where('created_at', '>=', now()->subHours(24));
        }

        $rawResults = $query->orderBy('created_at', 'asc')->with('machine')->get();

        // For summary cards (must be after $rawResults is defined)
        $totalMachines = Machine::count();
        $categories = [
            'Alert' => $rawResults->whereIn('condition_status', ['FAULT', 'CRITICAL'])->count(),
            'Warning' => $rawResults->where('condition_status', 'WARNING')->count(),
        ];


        // Ambil semua hasil analisis dari tabel analysis_results
        $rawResults = AnalysisResult::all();

        // Agregasi sesuai interval yang dipilih user (default 3 menit)
        $interval = (int) $request->input('aggregation_interval', 3);
        $allowedIntervals = [1, 3, 5, 10, 15];
        if (!in_array($interval, $allowedIntervals)) {
            $interval = 3;
        }
        $grouped = $rawResults->groupBy(function ($item) use ($interval) {
            $minute = floor($item->created_at->minute / $interval) * $interval;
            return $item->created_at->format('d-m H:') . str_pad($minute, 2, '0', STR_PAD_LEFT);
        });
        $rmsData = $grouped->map(function ($items, $label) {
            // Ambil data pertama di grup untuk info tambahan
            $first = $items->first();
            return [
                'time' => $label,
                'value' => round($items->avg('rms'), 4),
                'full_time' => $first ? $first->created_at->format('Y-m-d H:i:s') : null,
                'machine' => $first && $first->machine ? $first->machine->name : null,
                'status' => $first ? $first->condition_status : null
            ];
        })->values();

        // Ambil data suhu sesuai waktu label RMS (jika ada)
        $labels = $rmsData->pluck('time')->toArray();
        $fullTimes = $rmsData->pluck('full_time')->toArray();
        $temperatures = [];
        foreach ($fullTimes as $fullTime) {
            $temp = TemperatureReading::where('recorded_at', '<=', $fullTime)
                ->orderBy('recorded_at', 'desc')
                ->value('temperature_c');
            $temperatures[] = $temp !== null ? round($temp, 2) : null;
        }
        $rmsChartData = [
            'labels' => $labels,
            'values' => $rmsData->pluck('value')->toArray(),
            'full_times' => $fullTimes,
            'machines' => $rmsData->pluck('machine')->toArray(),
            'statuses' => $rmsData->pluck('status')->toArray(),
            'temperatures' => $temperatures,
        ];


        // Trend Chart Data - RMS harian untuk 7 hari terakhir
        $trendData = AnalysisResult::where('created_at', '>=', now()->subDays(7))
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function ($items, $date) {
                return [
                    'date' => \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('D, d M'),
                    'avg_rms' => round($items->avg('rms'), 4),
                    'max_rms' => round($items->max('rms'), 4),
                    'count' => $items->count(),
                ];
            })
            ->sortKeys()
            ->values();

        $trendChartData = [
            'labels' => $trendData->pluck('date')->toArray(),
            'avg_values' => $trendData->pluck('avg_rms')->toArray(),
            'max_values' => $trendData->pluck('max_rms')->toArray(),
        ];

        // FFT Chart Data (dummy, agar tidak error di Blade)
        $fftChartData = [
            'frequencies' => [],
            'amplitudes' => [],
            'peak_amp' => 0,
            'dominant_freq' => 0,
        ];

        // Distribusi status mesin (berdasarkan status terakhir tiap mesin dalam filter)
        $statusLabels = ['NORMAL', 'WARNING', 'FAULT', 'CRITICAL', 'ANOMALY'];
        $statusDistribution = array_fill_keys($statusLabels, 0);
        $latestStatusPerMachine = $rawResults->groupBy('machine_id')->map(function ($items) {
            return strtoupper(optional($items->last())->condition_status);
        });
        foreach ($latestStatusPerMachine as $status) {
            if (in_array($status, $statusLabels)) {
                $statusDistribution[$status]++;
            }
        }

        // ==================================================================================
        // MODUL ANALISIS (SCIENTIFIC LAYER)
        // ==================================================================================

        // 0. Data Preparation
        $rmsValues = $rmsData->pluck('value');
        $currentRMS = $rmsValues->last() ?? 0;

        // A. Analisis Statistik Getaran (Stability Analysis)
        $stats = [
            'min' => $rmsValues->min() ?? 0,
            'max' => $rmsValues->max() ?? 0,
            'avg' => $rmsValues->avg() ?? 0,
            'count' => $rmsValues->count(),
            'std_dev' => 0
        ];
        // Calculate Standard Deviation for Stability Check
        if ($stats['count'] > 1) {
            $variance = $rmsValues->map(function ($val) use ($stats) {
                return pow($val - $stats['avg'], 2);
            })->avg();
            $stats['std_dev'] = sqrt($variance);
        }

        // B. Analisis Threshold & Severity Level
        // Use per-machine threshold from database
        $machine = Machine::find($machineId);
        $thresholds = [
            'warning' => (float) ($machine->threshold_warning ?? 1.8),
            'critical' => (float) ($machine->threshold_critical ?? 4.5)
        ];
        $machineStatus = 'NORMAL';

        if ($currentRMS >= $thresholds['critical']) {
            $machineStatus = 'CRITICAL';
        } elseif ($currentRMS >= $thresholds['warning']) {
            $machineStatus = 'WARNING';
        }

        // D. Analisis Tren (Predictive)
        $trendAnalysis = [
            'direction' => 'Stabil',
            'change_percent' => 0,
            'is_significant' => false
        ];

        if ($rmsValues->count() >= 2) {
            $first = $rmsValues->first();
            $last = $rmsValues->last();
            if ($first > 0) {
                $diff = $last - $first;
                $percent = ($diff / $first) * 100;
                $trendAnalysis['change_percent'] = round($percent, 1);

                if ($percent > 10) {
                    $trendAnalysis['direction'] = 'Naik (Degradasi)';
                    $trendAnalysis['is_significant'] = true;
                } elseif ($percent < -10) {
                    $trendAnalysis['direction'] = 'Turun (Membaik)';
                }
            }
        }

        // E. Klasifikasi & Kesimpulan (Explainable Output)
        $conclusion = "Mesin beroperate dalam kondisi {$machineStatus}.";
        $reasons = [];

        if ($machineStatus != 'NORMAL') {
            $reasons[] = "RMS ({$currentRMS} mm/s) > batas " . strtolower($machineStatus);
        }
        if ($trendAnalysis['is_significant'] && $trendAnalysis['change_percent'] > 0) {
            $reasons[] = "tren naik {$trendAnalysis['change_percent']}%";
        }

        if (!empty($reasons)) {
            $conclusion = "PERINGATAN: " . ucfirst(implode(', ', $reasons)) . ".";
        }

        // G. Rekomendasi
        $recommendation = "Lanjutkan pemantauan rutin.";
        if ($machineStatus == 'WARNING') {
            $recommendation = "Jadwalkan inspeksi visual & cek pelumasan.";
        } elseif ($machineStatus == 'CRITICAL') {
            $recommendation = "STOP MESIN & lakukan maintenance segera.";
        }

        $analysisInsights = compact('stats', 'machineStatus', 'thresholds', 'trendAnalysis', 'conclusion', 'recommendation');

        return view('pages.data-grafik', compact(
            'machines',
            'latestDate',
            'earliestDate',
            'rmsChartData',
            'trendChartData',
            'fftChartData',
            'rawResults',
            'lastUpdate',
            'totalMachines',
            'categories',
            'statusDistribution',
            'worstMachines',
            'analysisInsights'
        ));
    }

    public function analisis()
    {
        // Get all machines for dropdown
        $machines = Machine::orderBy('name')->get();

        // Hitung jumlah mesin per status terakhir
        $countNormal = 0;
        $countAnomaly = 0;
        $countWarning = 0;
        $countFault = 0;
        $countCritical = 0;
        $lastAnalysisTime = null;

        foreach ($machines as $machine) {
            $latest = $machine->latestAnalysis;
            if ($latest) {
                $status = strtoupper($latest->condition_status ?? '');
                if ($status === 'NORMAL')
                    $countNormal++;
                elseif ($status === 'ANOMALY')
                    $countAnomaly++;
                elseif ($status === 'WARNING')
                    $countWarning++;
                elseif ($status === 'FAULT')
                    $countFault++;
                elseif ($status === 'CRITICAL')
                    $countCritical++;

                if (!$lastAnalysisTime || $latest->created_at > $lastAnalysisTime) {
                    $lastAnalysisTime = $latest->created_at;
                }
            }
        }
        $lastAnalysisTime = $lastAnalysisTime ? $lastAnalysisTime->locale('id')->translatedFormat('l, d M Y, H:i') : '-';

        return view('pages.analisis', compact(
            'machines',
            'countNormal',
            'countAnomaly',
            'countWarning',
            'countFault',
            'countCritical',
            'lastAnalysisTime'
        ));
    }

    public function getMachineAlerts($id)
    {
        try {
            $machine = Machine::findOrFail($id);

            // Get per-machine threshold from database
            $warningThreshold = (float) ($machine->threshold_warning ?? 1.8);
            $criticalThreshold = (float) ($machine->threshold_critical ?? 4.5);

            // Get recent anomaly analyses as alerts
            $alerts = AnalysisResult::where('machine_id', $id)
                ->where('condition_status', '!=', 'NORMAL')
                ->where('created_at', '>=', now()->subHours(24))
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($analysis) use ($warningThreshold, $criticalThreshold) {
                    // Use per-machine threshold
                    $severity = 'WARNING';
                    if ($analysis->rms >= $criticalThreshold) {
                        $severity = 'CRITICAL';  // Danger zone
                    } elseif ($analysis->rms >= $warningThreshold) {
                        $severity = 'WARNING';   // Warning zone
                    } else {
                        $severity = 'NORMAL';    // Good zone
                    }
                    return [
                        'id' => $analysis->id,
                        'severity' => $severity,
                        'message' => "Anomaly detected: RMS={$analysis->rms}, Peak={$analysis->peak_amp}G",
                        'created_at' => $analysis->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'alerts' => $alerts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
