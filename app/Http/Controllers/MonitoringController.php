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
            ->where('status', 'success')
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
}
