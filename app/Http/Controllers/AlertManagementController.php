<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlertManagementController extends Controller
{
    /**
     * Display alert management page
     */
    public function index()
    {
        $machines = Machine::all();

        // Get threshold config from cache or default
        // ISO 10816-3 Thresholds (mm/s) for Medium Machines (Class II)
        $thresholdConfig = Cache::get('alert_threshold_config', [
            'warning' => 2.8,  // ISO 10816-3 Zone B
            'critical' => 7.1, // ISO 10816-3 Zone C/D
        ]);

        // Get notification config
        $notificationConfig = Cache::get('alert_notification_config', [
            'email_enabled' => false,
            'email_recipients' => '',
            'auto_acknowledge_hours' => 24,
            'alert_sound_enabled' => false,
        ]);

        return view('pages.alert-management', compact('machines', 'thresholdConfig', 'notificationConfig'));
    }

    /**
     * Get all alerts with filters
     */
    public function getAlerts(Request $request)
    {
        try {
            $query = AnalysisResult::with('machine')
                ->whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->orderBy('created_at', 'desc');

            // Filter by machine
            if ($request->filled('machine_id')) {
                $query->where('machine_id', $request->machine_id);
            }

            // Filter by severity
            if ($request->filled('severity')) {
                $thresholds = Cache::get('alert_threshold_config', [
                    'warning' => 2.8,
                    'critical' => 7.1,
                ]);

                switch ($request->severity) {
                    case 'critical':
                        $query->where('rms', '>=', $thresholds['critical']);
                        break;
                    case 'warning':
                        $query->where('rms', '>=', $thresholds['warning'])
                              ->where('rms', '<', $thresholds['critical']);
                        break;
                }
            }

            // Filter by status (acknowledged/unacknowledged)
            if ($request->filled('status')) {
                // This will be handled client-side since acknowledgment is cached
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $alerts = $query->paginate($request->get('per_page', 15));

            $thresholds = Cache::get('alert_threshold_config', [
                'warning' => 2.8,
                'critical' => 7.1,
            ]);

            $alerts->getCollection()->transform(function ($alert) use ($thresholds) {
                return [
                    'id' => $alert->id,
                    'machine_id' => $alert->machine_id,
                    'machine_name' => $alert->machine->name ?? 'Unknown',
                    'location' => $alert->machine->location ?? 'Unknown',
                    'rms' => round($alert->rms, 4),
                    'peak_amp' => round($alert->peak_amp ?? 0, 4),
                    'dominant_freq_hz' => round($alert->dominant_freq_hz ?? 0, 2),
                    'severity' => $this->getSeverityLevel($alert->rms, $thresholds),
                    'severity_label' => $this->getSeverityLabel($alert->rms, $thresholds),
                    'condition_status' => $alert->condition_status,
                    'timestamp' => $alert->created_at->format('Y-m-d H:i:s'),
                    'time_ago' => $alert->created_at->diffForHumans(),
                    'acknowledged' => Cache::get("alert_ack_{$alert->id}", false),
                    'acknowledged_at' => Cache::get("alert_ack_time_{$alert->id}"),
                    'acknowledged_by' => Cache::get("alert_ack_by_{$alert->id}"),
                    'resolved' => Cache::get("alert_resolved_{$alert->id}", false),
                    'resolved_at' => Cache::get("alert_resolved_time_{$alert->id}"),
                    'notes' => Cache::get("alert_notes_{$alert->id}", ''),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $alerts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching alerts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get alert statistics
     */
    public function getStats()
    {
        try {
            $thresholds = Cache::get('alert_threshold_config', [
                'warning' => 2.8,
                'critical' => 7.1,
            ]);

            $baseQuery = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER']);

            // Today's alerts
            $todayAlerts = (clone $baseQuery)->whereDate('created_at', today())->count();

            // Last 24 hours
            $last24h = (clone $baseQuery)->where('created_at', '>=', now()->subHours(24))->count();

            // Last 7 days
            $last7days = (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count();

            // By severity (last 24h) - 2 levels only
            $criticalCount = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->where('created_at', '>=', now()->subHours(24))
                ->where('rms', '>=', $thresholds['critical'])
                ->count();

            $warningCount = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->where('created_at', '>=', now()->subHours(24))
                ->where('rms', '>=', $thresholds['warning'])
                ->where('rms', '<', $thresholds['critical'])
                ->count();

            // By machine
            $machineStats = Machine::withCount(['analysisResults as alert_count' => function ($query) {
                $query->whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                      ->where('created_at', '>=', now()->subHours(24));
            }])->get()->map(function ($machine) {
                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'location' => $machine->location,
                    'alert_count' => $machine->alert_count,
                ];
            });

            // Acknowledged count
            $acknowledgedCount = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->where('created_at', '>=', now()->subHours(24))
                ->get()
                ->filter(fn($a) => Cache::get("alert_ack_{$a->id}", false))
                ->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'today' => $todayAlerts,
                    'last_24h' => $last24h,
                    'last_7_days' => $last7days,
                    'by_severity' => [
                        'critical' => $criticalCount,
                        'warning' => $warningCount,
                    ],
                    'acknowledged' => $acknowledgedCount,
                    'unacknowledged' => $last24h - $acknowledgedCount,
                    'by_machine' => $machineStats,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stats: ' . $e->getMessage(),
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

            Cache::put("alert_ack_{$id}", true, now()->addDays(7));
            Cache::put("alert_ack_time_{$id}", now()->format('Y-m-d H:i:s'), now()->addDays(7));
            Cache::put("alert_ack_by_{$id}", auth()->user()->name ?? 'System', now()->addDays(7));

            if ($request->filled('notes')) {
                Cache::put("alert_notes_{$id}", $request->notes, now()->addDays(7));
            }

            return response()->json([
                'success' => true,
                'message' => 'Alert acknowledged successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error acknowledging alert: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(Request $request, $id)
    {
        try {
            $alert = AnalysisResult::findOrFail($id);

            Cache::put("alert_resolved_{$id}", true, now()->addDays(30));
            Cache::put("alert_resolved_time_{$id}", now()->format('Y-m-d H:i:s'), now()->addDays(30));
            Cache::put("alert_resolved_by_{$id}", auth()->user()->name ?? 'System', now()->addDays(30));

            if ($request->filled('resolution_notes')) {
                Cache::put("alert_resolution_notes_{$id}", $request->resolution_notes, now()->addDays(30));
            }

            return response()->json([
                'success' => true,
                'message' => 'Alert resolved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resolving alert: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk acknowledge alerts
     */
    public function bulkAcknowledge(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $count = 0;

            foreach ($ids as $id) {
                if (AnalysisResult::find($id)) {
                    Cache::put("alert_ack_{$id}", true, now()->addDays(7));
                    Cache::put("alert_ack_time_{$id}", now()->format('Y-m-d H:i:s'), now()->addDays(7));
                    Cache::put("alert_ack_by_{$id}", auth()->user()->name ?? 'System', now()->addDays(7));
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} alerts acknowledged successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error acknowledging alerts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update threshold configuration
     */
    public function updateThresholds(Request $request)
    {
        try {
            $validated = $request->validate([
                'warning' => 'required|numeric|min:0',
                'critical' => 'required|numeric|min:0',
            ]);

            // Validate that thresholds are in ascending order
            if ($validated['warning'] >= $validated['critical']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thresholds must be in ascending order: Warning < Critical',
                ], 422);
            }

            Cache::forever('alert_threshold_config', $validated);

            return response()->json([
                'success' => true,
                'message' => 'Threshold configuration updated successfully',
                'config' => $validated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating thresholds: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update notification configuration
     */
    public function updateNotifications(Request $request)
    {
        try {
            $validated = $request->validate([
                'email_enabled' => 'boolean',
                'email_recipients' => 'nullable|string',
                'auto_acknowledge_hours' => 'integer|min:1|max:168',
                'alert_sound_enabled' => 'boolean',
            ]);

            Cache::forever('alert_notification_config', $validated);

            return response()->json([
                'success' => true,
                'message' => 'Notification configuration updated successfully',
                'config' => $validated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating notifications: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get alert history (resolved alerts)
     */
    public function getHistory(Request $request)
    {
        try {
            $query = AnalysisResult::with('machine')
                ->whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->orderBy('created_at', 'desc');

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            } else {
                $query->where('created_at', '>=', now()->subDays(30));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('machine_id')) {
                $query->where('machine_id', $request->machine_id);
            }

            $thresholds = Cache::get('alert_threshold_config', [
                'warning' => 2.8,
                'critical' => 7.1,
                'danger' => 11.2,
            ]);

            $alerts = $query->paginate($request->get('per_page', 20));

            $alerts->getCollection()->transform(function ($alert) use ($thresholds) {
                return [
                    'id' => $alert->id,
                    'machine_name' => $alert->machine->name ?? 'Unknown',
                    'location' => $alert->machine->location ?? 'Unknown',
                    'rms' => round($alert->rms, 4),
                    'severity' => $this->getSeverityLevel($alert->rms, $thresholds),
                    'severity_label' => $this->getSeverityLabel($alert->rms, $thresholds),
                    'timestamp' => $alert->created_at->format('Y-m-d H:i:s'),
                    'acknowledged' => Cache::get("alert_ack_{$alert->id}", false),
                    'acknowledged_at' => Cache::get("alert_ack_time_{$alert->id}"),
                    'acknowledged_by' => Cache::get("alert_ack_by_{$alert->id}"),
                    'resolved' => Cache::get("alert_resolved_{$alert->id}", false),
                    'resolved_at' => Cache::get("alert_resolved_time_{$alert->id}"),
                    'resolution_notes' => Cache::get("alert_resolution_notes_{$alert->id}", ''),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $alerts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export alerts to CSV
     */
    public function exportAlerts(Request $request)
    {
        try {
            $query = AnalysisResult::with('machine')
                ->whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('machine_id')) {
                $query->where('machine_id', $request->machine_id);
            }

            $thresholds = Cache::get('alert_threshold_config', [
                'warning' => 2.8,
                'critical' => 7.1,
                'danger' => 11.2,
            ]);

            $alerts = $query->get();

            $csvData = "ID,Machine,Location,RMS (mm/s),Severity,Status,Timestamp,Acknowledged,Acknowledged By,Acknowledged At\n";

            foreach ($alerts as $alert) {
                $ack = Cache::get("alert_ack_{$alert->id}", false) ? 'Yes' : 'No';
                $ackBy = Cache::get("alert_ack_by_{$alert->id}", '-');
                $ackAt = Cache::get("alert_ack_time_{$alert->id}", '-');

                $csvData .= implode(',', [
                    $alert->id,
                    '"' . ($alert->machine->name ?? 'Unknown') . '"',
                    '"' . ($alert->machine->location ?? 'Unknown') . '"',
                    round($alert->rms, 4),
                    $this->getSeverityLabel($alert->rms, $thresholds),
                    $alert->condition_status,
                    $alert->created_at->format('Y-m-d H:i:s'),
                    $ack,
                    '"' . $ackBy . '"',
                    $ackAt,
                ]) . "\n";
            }

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="alerts_export_' . date('Y-m-d_His') . '.csv"');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting alerts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get severity level based on RMS value
     */
    private function getSeverityLevel($rms, $thresholds)
    {
        if ($rms >= $thresholds['critical']) {
            return 'critical';
        } elseif ($rms >= $thresholds['warning']) {
            return 'warning';
        }
        return 'normal';
    }

    /**
     * Get severity label based on RMS value
     */
    private function getSeverityLabel($rms, $thresholds)
    {
        if ($rms >= $thresholds['critical']) {
            return 'Bahaya';
        } elseif ($rms >= $thresholds['warning']) {
            return 'Peringatan';
        }
        return 'Normal';
    }
}
