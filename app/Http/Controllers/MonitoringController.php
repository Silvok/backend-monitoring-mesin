<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\AnalysisResult;
use App\Models\RawSample;
use App\Models\TemperatureReading;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index()
    {
        $machines = Machine::orderBy('name')->get();
        return view('pages.monitoring-mesin', compact('machines'));
    }

    public function getMonitoringData(Request $request)
    {
        $machineId = $request->machine_id;
        $range = $request->range ?? '24h';
        $axis = $request->axis ?? 'resultant';
        $start = $request->start;
        $end = $request->end;

        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }

        // Query for RMS Vibration (Time Domain Visualization)
        $query = AnalysisResult::where('machine_id', $machineId);

        if ($range === '1h') {
            $query->where('created_at', '>=', now()->subHour());
        } elseif ($range === '24h') {
            $query->where('created_at', '>=', now()->subHours(24));
        } elseif ($range === '7d') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($range === 'realtime') {
            $query->where('created_at', '>=', now()->subMinutes(10));
        } elseif ($range === 'custom' && $start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $vibrationData = $query->orderBy('created_at', 'asc')->get();

        // Query for Temperature
        $tempQuery = TemperatureReading::where('machine_id', $machineId);
        if ($range === '1h') {
            $tempQuery->where('recorded_at', '>=', now()->subHour());
        } elseif ($range === '24h') {
            $tempQuery->where('recorded_at', '>=', now()->subHours(24));
        } elseif ($range === '7d') {
            $tempQuery->where('recorded_at', '>=', now()->subDays(7));
        } elseif ($range === 'realtime') {
            $tempQuery->where('recorded_at', '>=', now()->subMinutes(10));
        } elseif ($range === 'custom' && $start && $end) {
            $tempQuery->whereBetween('recorded_at', [$start, $end]);
        }
        $temperatureData = $tempQuery->orderBy('recorded_at', 'asc')->get();

        // Get Latest FFT Data for the last analysis
        $latestAnalysis = AnalysisResult::where('machine_id', $machineId)
            ->has('fftResult')
            ->latest()
            ->first();

        $fftData = null;
        if ($latestAnalysis && $latestAnalysis->fftResult) {
            $fftData = [
                'frequencies' => $latestAnalysis->fftResult->frequencies,
                'amplitudes' => $latestAnalysis->fftResult->amplitudes,
                'dominant_freq_hz' => $latestAnalysis->dominant_freq_hz,
                'peak_amp' => $latestAnalysis->peak_amp,
                'timestamp' => $latestAnalysis->created_at->translatedFormat('d M Y, H:i:s')
            ];
        }

        // Format data for Chart.js (Scatter/Line with Time Axis)
        $formattedVibration = $vibrationData->map(function ($v) {
            return [
                'x' => $v->created_at->timestamp * 1000,
                'y' => (float) $v->rms
            ];
        });

        $formattedTemperature = $temperatureData->map(function ($t) {
            return [
                'x' => Carbon::parse($t->recorded_at)->timestamp * 1000,
                'y' => (float) $t->temperature_c
            ];
        });

        return response()->json([
            'status' => 'success',
            'time_domain' => [
                'vibration' => $formattedVibration,
                'temperature' => $formattedTemperature,
            ],
            'frequency_domain' => $fftData,
            'summary' => [
                'max_rms' => round($vibrationData->max('rms') ?? 0, 4),
                'avg_rms' => round($vibrationData->avg('rms') ?? 0, 4),
                'max_temp' => round($temperatureData->max('temperature_c') ?? 0, 2),
                'latest_status' => $vibrationData->last()?->condition_status ?? 'UNKNOWN'
            ]
        ]);
    }

    public function getTrendData(Request $request)
    {
        $machineId = $request->machine_id;
        $period = $request->period ?? 'daily'; // daily or weekly

        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }

        $query = AnalysisResult::where('machine_id', $machineId);

        if ($period === 'weekly') {
            // Group by Year and Week
            $data = $query->select(
                DB::raw('YEARWEEK(created_at, 1) as period_label'),
                DB::raw('MIN(created_at) as timestamp'),
                DB::raw('AVG(rms) as avg_rms'),
                DB::raw('MAX(rms) as max_rms')
            )
                ->groupBy('period_label')
                ->orderBy('timestamp', 'asc')
                ->get();
        } else {
            // Default Daily Grouping
            $data = $query->select(
                DB::raw('DATE(created_at) as period_label'),
                DB::raw('MIN(created_at) as timestamp'),
                DB::raw('AVG(rms) as avg_rms'),
                DB::raw('MAX(rms) as max_rms')
            )
                ->groupBy('period_label')
                ->orderBy('period_label', 'asc')
                ->get();
        }

        $formatted = $data->map(function ($item) {
            return [
                'label' => Carbon::parse($item->timestamp)->translatedFormat('d M'),
                'timestamp' => Carbon::parse($item->timestamp)->timestamp * 1000,
                'avg_rms' => round($item->avg_rms, 4),
                'max_rms' => round($item->max_rms, 4)
            ];
        });

        return response()->json([
            'status' => 'success',
            'trend' => $formatted
        ]);
    }

    /**
     * Analisis Tren RMS - Early Warning Detection
     * Menghitung perubahan persentase RMS dalam 24 jam terakhir
     */
    public function getTrendAnalysis(Request $request)
    {
        $machineId = $request->machine_id;

        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }

        // Ambil data 24 jam terakhir
        $last24h = AnalysisResult::where('machine_id', $machineId)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->get();

        // Ambil data 24-48 jam lalu (untuk perbandingan)
        $prev24h = AnalysisResult::where('machine_id', $machineId)
            ->where('created_at', '>=', now()->subHours(48))
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        // Hitung statistik
        $currentAvg = $last24h->avg('rms') ?? 0;
        $currentMax = $last24h->max('rms') ?? 0;
        $currentMin = $last24h->min('rms') ?? 0;
        $currentCount = $last24h->count();

        $prevAvg = $prev24h->avg('rms') ?? 0;

        // Hitung persentase perubahan
        $changePercent = 0;
        if ($prevAvg > 0) {
            $changePercent = round((($currentAvg - $prevAvg) / $prevAvg) * 100, 1);
        }

        // Tentukan arah tren
        $trendDirection = 'stable';
        $isSignificant = false;
        if ($changePercent > 10) {
            $trendDirection = 'increasing';
            $isSignificant = true;
        } elseif ($changePercent < -10) {
            $trendDirection = 'decreasing';
            $isSignificant = false;
        }

        // Tentukan severity level
        $severity = 'info';
        $alertMessage = "Nilai RMS stabil dalam 24 jam terakhir.";

        if ($changePercent > 20) {
            $severity = 'danger';
            $alertMessage = "Terjadi peningkatan RMS sebesar " . abs($changePercent) . "% dalam 24 jam terakhir.";
        } elseif ($changePercent > 10) {
            $severity = 'warning';
            $alertMessage = "Terjadi peningkatan RMS sebesar " . abs($changePercent) . "% dalam 24 jam terakhir.";
        } elseif ($changePercent < -10) {
            $severity = 'success';
            $alertMessage = "Terjadi penurunan RMS sebesar " . abs($changePercent) . "% dalam 24 jam terakhir.";
        }

        // Buat rekomendasi
        $recommendation = "Lanjutkan pemantauan rutin.";
        if ($severity === 'danger') {
            $recommendation = "KRITIS: Peningkatan signifikan terdeteksi! Segera lakukan inspeksi mesin.";
        } elseif ($severity === 'warning') {
            $recommendation = "PERINGATAN: Tren kenaikan getaran perlu diperhatikan. Jadwalkan pemeriksaan.";
        } elseif ($severity === 'success') {
            $recommendation = "MEMBAIK: Kondisi mesin menunjukkan perbaikan. Lanjutkan pemantauan.";
        }

        // Hitung standard deviation untuk stability check
        $stdDev = 0;
        if ($currentCount > 1) {
            $variance = $last24h->map(function ($item) use ($currentAvg) {
                return pow($item->rms - $currentAvg, 2);
            })->avg();
            $stdDev = sqrt($variance);
        }

        return response()->json([
            'status' => 'success',
            'trend_analysis' => [
                'current_avg' => round($currentAvg, 4),
                'current_max' => round($currentMax, 4),
                'current_min' => round($currentMin, 4),
                'previous_avg' => round($prevAvg, 4),
                'change_percent' => $changePercent,
                'trend_direction' => $trendDirection,
                'is_significant' => $isSignificant,
                'severity' => $severity,
                'alert_message' => $alertMessage,
                'recommendation' => $recommendation,
                'std_dev' => round($stdDev, 4),
                'data_count' => $currentCount
            ]
        ]);
    }
}
