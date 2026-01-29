<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalysisResult;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlertController extends Controller
{
    /**
     * Get active alerts (anomalies)
     */
    public function getActiveAlerts()
    {
        try {
            // Cache alerts for 15 seconds
            $alerts = Cache::remember('api_active_alerts', 15, function () {
                return AnalysisResult::with('machine')
                    ->where('condition_status', 'ANOMALY')
                    ->where('created_at', '>=', now()->subDay())
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($analysis) {
                        return [
                            'id' => $analysis->id,
                            'machine_id' => $analysis->machine_id,
                            'machine_name' => $analysis->machine->name ?? 'Unknown',
                            'location' => $analysis->machine->location ?? 'Unknown',
                            'status' => 'anomaly',
                            'severity' => $this->calculateSeverity($analysis),
                            'rms' => $analysis->rms,
                            'peak_amp' => $analysis->peak_amp,
                            'dominant_freq_hz' => $analysis->dominant_freq_hz,
                            'timestamp' => $analysis->created_at->toDateTimeString(),
                            'time_ago' => $analysis->created_at->diffForHumans(),
                            'acknowledged' => Cache::get("alert_ack_{$analysis->id}", false),
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'alerts' => $alerts,
                'total' => $alerts->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching alerts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Acknowledge an alert
     */
    public function acknowledgeAlert(Request $request, $id)
    {
        try {
            $alert = AnalysisResult::findOrFail($id);

            // Store acknowledgment in cache for 24 hours
            Cache::put("alert_ack_{$id}", true, now()->addDay());

            return response()->json([
                'success' => true,
                'message' => 'Alert acknowledged',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error acknowledging alert: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dismiss all alerts for a machine
     */
    public function dismissMachineAlerts(Request $request, $machineId)
    {
        try {
            $alerts = AnalysisResult::where('machine_id', $machineId)
                ->where('condition_status', 'ANOMALY')
                ->where('created_at', '>=', now()->subDay())
                ->get();

            foreach ($alerts as $alert) {
                Cache::put("alert_ack_{$alert->id}", true, now()->addDay());
            }

            return response()->json([
                'success' => true,
                'message' => 'All alerts dismissed for machine',
                'dismissed_count' => $alerts->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error dismissing alerts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get alert statistics
     */
    public function getAlertStats()
    {
        try {
            $total = AnalysisResult::where('condition_status', 'ANOMALY')
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $critical = AnalysisResult::where('condition_status', 'ANOMALY')
                ->where('created_at', '>=', now()->subDay())
                ->where('rms', '>=', 7.1)
                ->count();

            $acknowledged = collect(range(1, 1000))
                ->filter(fn($id) => Cache::get("alert_ack_{$id}", false))
                ->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total' => $total,
                    'critical' => $critical,
                    'warning' => $total - $critical,
                    'acknowledged' => $acknowledged,
                    'unacknowledged' => $total - $acknowledged,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching alert stats: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate severity based on RMS value
     */
    private function calculateSeverity($analysis)
    {
        $warningThreshold = (float) ($analysis->machine->threshold_warning ?? 2.8);
        $criticalThreshold = (float) ($analysis->machine->threshold_critical ?? 7.1);

        if ($analysis->rms >= $criticalThreshold) {
            return 'critical';
        } elseif ($analysis->rms >= $warningThreshold) {
            return 'high';
        }
        return 'low';
    }
}
