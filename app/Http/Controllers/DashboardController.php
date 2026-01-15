<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\RawSample;
use App\Models\AnalysisResult;
use App\Models\TemperatureReading;
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
        $machine = Machine::with([
            'latestAnalysis',
            'rawSamples' => function ($query) {
                $query->latest()->limit(10);
            }
        ])->first();

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

        // Get latest temperature readings
        $latestTemperatureData = TemperatureReading::with('machine')
            ->latest('recorded_at')
            ->limit(10)
            ->get();

        // Calculate anomaly count
        $anomalyCount = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'FAULT', 'CRITICAL'])->count();
        $normalCount = AnalysisResult::where('condition_status', 'NORMAL')->count();

        // Get RMS data for last 24 hours for chart
        $rmsData = AnalysisResult::where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->get(['rms', 'created_at'])
            ->map(function ($item) {
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
            'latestTemperatureData',
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

        $rmsChartData = [
            'labels' => $rmsData->pluck('time')->toArray(),
            'values' => $rmsData->pluck('value')->toArray(),
            'full_times' => $rmsData->pluck('full_time')->toArray(),
            'machines' => $rmsData->pluck('machine')->toArray(),
            'statuses' => $rmsData->pluck('status')->toArray(),
        ];

        // FFT Chart Data - Data frekuensi dari analysis results
        $fftData = $rawResults->map(function ($item) {
            return [
                'freq' => round($item->dominant_freq_hz ?? 0, 2),
                'amp' => round($item->peak_amp ?? 0, 4),
                'time' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        })->values();

        $fftChartData = [
            'frequencies' => $fftData->pluck('freq')->toArray(),
            'amplitudes' => $fftData->pluck('amp')->toArray(),
            'times' => $fftData->pluck('time')->toArray(),
            'dominant_freq' => $rawResults->max('dominant_freq_hz') ?? 0,
            'peak_amp' => $rawResults->max('peak_amp') ?? 0,
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
        // Thresholds adjusted for 'g' unit (Approximate ISO 10816-3 scale for demo)
        $thresholds = ['warning' => 0.5, 'critical' => 1.0];
        $machineStatus = 'NORMAL';

        if ($currentRMS >= $thresholds['critical']) {
            $machineStatus = 'CRITICAL';
        } elseif ($currentRMS >= $thresholds['warning']) {
            $machineStatus = 'WARNING';
        }

        // C. Analisis Spektrum & Diagnosa (FFT Insights)
        $latestRaw = $rawResults->last();
        $fftAnalysis = [
            'dominant_freq' => $latestRaw ? round($latestRaw->dominant_freq_hz, 2) : 0,
            'peak_amp' => $latestRaw ? round($latestRaw->peak_amp, 4) : 0,
            'indication' => 'Normal',
            'diagnosis' => 'Tidak ada anomali frekuensi signifikan.'
        ];

        if ($latestRaw && $latestRaw->dominant_freq_hz > 0) {
            $freq = $latestRaw->dominant_freq_hz;
            if ($freq < 50) {
                $fftAnalysis['indication'] = 'Unbalance';
                $fftAnalysis['diagnosis'] = "Dominasi frekuensi rendah ({$freq} Hz). Kemungkinan unbalance atau kekenduran.";
            } elseif ($freq < 200) {
                $fftAnalysis['indication'] = 'Misalignment';
                $fftAnalysis['diagnosis'] = "Dominasi frekuensi menengah ({$freq} Hz). Indikasi potensi misalignment.";
            } else {
                $fftAnalysis['indication'] = 'Bearing Fault';
                $fftAnalysis['diagnosis'] = "Dominasi frekuensi tinggi ({$freq} Hz). Waspadai kerusakan bantalan (bearing).";
            }
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
            $reasons[] = "RMS ({$currentRMS} g) > batas " . strtolower($machineStatus);
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

        $analysisInsights = compact('stats', 'machineStatus', 'thresholds', 'fftAnalysis', 'trendAnalysis', 'conclusion', 'recommendation');

        return view('pages.data-grafik', compact(
            'machines',
            'latestDate',
            'earliestDate',
            'rmsChartData',
            'fftChartData',
            'trendChartData',
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

            // Get recent anomaly analyses as alerts
            $alerts = AnalysisResult::where('machine_id', $id)
                ->where('condition_status', '!=', 'NORMAL')
                ->where('created_at', '>=', now()->subHours(24))
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($analysis) {
                    $severity = 'WARNING';
                    if ($analysis->rms >= 1.8) {
                        $severity = 'CRITICAL';
                    } elseif ($analysis->rms >= 0.7) {
                        $severity = 'WARNING';
                    } else {
                        $severity = 'NORMAL';
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
