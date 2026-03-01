<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlertManagementController extends Controller
{
    /**
     * Default threshold presets by class
     */
    private const THRESHOLD_PRESETS = [
        'Class I' => ['warning' => 21.84, 'critical' => 25.11],
        'Class II' => ['warning' => 21.84, 'critical' => 25.11],
        'Class III' => ['warning' => 21.84, 'critical' => 25.11],
        'Class IV' => ['warning' => 21.84, 'critical' => 25.11],
    ];

    /**
     * Display alert management page
     */
    public function index()
    {
        $machines = Machine::all();

        // Get notification config
        $notificationConfig = Cache::get('alert_notification_config', [
            'email_enabled' => false,
            'email_recipients' => '',
            'auto_acknowledge_hours' => 24,
            'alert_sound_enabled' => false,
        ]);

        return view('pages.alert-management', compact('machines', 'notificationConfig'));
    }

    /**
     * Get threshold for a specific machine
     */
    private function getMachineThreshold(Machine $machine): array
    {
        return [
            'warning' => (float) ($machine->threshold_warning ?? 21.84),
            'critical' => (float) ($machine->threshold_critical ?? 25.11),
        ];
    }

    /**
     * Get default threshold (used when no machine context)
     */
    private function getDefaultThreshold(): array
    {
        return self::THRESHOLD_PRESETS['Class II'];
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

            // Filter by severity - now uses per-machine thresholds
            if ($request->filled('severity')) {
                // Will be handled after fetch to use per-machine thresholds
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

            $alerts->getCollection()->transform(function ($alert) {
                // Use per-machine threshold
                $thresholds = $alert->machine
                    ? $this->getMachineThreshold($alert->machine)
                    : $this->getDefaultThreshold();

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
                    'resolved_by' => Cache::get("alert_resolved_by_{$alert->id}"),
                    'notes' => Cache::get("alert_notes_{$alert->id}", ''),
                    'threshold_warning' => $thresholds['warning'],
                    'threshold_critical' => $thresholds['critical'],
                ];
            });

            // Remove resolved alerts from active list
            $alerts->setCollection(
                $alerts->getCollection()
                    ->filter(fn($alert) => empty($alert['resolved']))
                    ->values()
            );

            // Filter by severity after transformation
            if ($request->filled('severity')) {
                $severity = $request->severity;
                $filtered = $alerts->getCollection()->filter(function ($alert) use ($severity) {
                    return $alert['severity'] === $severity;
                })->values();
                $alerts->setCollection($filtered);
            }

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
            $baseQuery = AnalysisResult::whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER']);

            // Today's alerts
            $todayAlerts = (clone $baseQuery)->whereDate('created_at', today())->count();

            // Last 24 hours
            $last24h = (clone $baseQuery)->where('created_at', '>=', now()->subHours(24))->count();

            // Last 7 days
            $last7days = (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count();

            // By severity (last 24h) - using per-machine thresholds
            $recentAlerts = AnalysisResult::with('machine')
                ->whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                ->where('created_at', '>=', now()->subHours(24))
                ->get();

            $criticalCount = 0;
            $warningCount = 0;

            foreach ($recentAlerts as $alert) {
                $thresholds = $alert->machine
                    ? $this->getMachineThreshold($alert->machine)
                    : $this->getDefaultThreshold();

                if ($alert->rms >= $thresholds['critical']) {
                    $criticalCount++;
                } elseif ($alert->rms >= $thresholds['warning']) {
                    $warningCount++;
                }
            }

            // By machine with threshold info
            $machineStats = Machine::withCount(['analysisResults as alert_count' => function ($query) {
                $query->whereIn('condition_status', ['ANOMALY', 'WARNING', 'CRITICAL', 'DANGER'])
                      ->where('created_at', '>=', now()->subHours(24));
            }])->get()->map(function ($machine) {
                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'location' => $machine->location,
                    'alert_count' => $machine->alert_count,
                    'threshold_warning' => (float) ($machine->threshold_warning ?? 21.84),
                    'threshold_critical' => (float) ($machine->threshold_critical ?? 25.11),
                    'iso_class' => $machine->iso_class ?? 'Class II',
                    'motor_power_hp' => $machine->motor_power_hp,
                ];
            });

            // Acknowledged count
            $acknowledgedCount = $recentAlerts
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
     * Update threshold configuration for a specific machine
     */
    public function updateThresholds(Request $request)
    {
        try {
            $validated = $request->validate([
                'machine_id' => 'required|exists:machines,id',
                'warning' => 'required|numeric|min:0',
                'critical' => 'required|numeric|min:0',
                'motor_power_hp' => 'nullable|numeric|min:0',
                'motor_rpm' => 'nullable|integer|min:0',
                'iso_class' => 'nullable|string|in:Class I,Class II,Class III,Class IV',
            ]);

            // Validate that thresholds are in ascending order
            if ($validated['warning'] >= $validated['critical']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thresholds must be in ascending order: Warning < Critical',
                ], 422);
            }

            $machine = Machine::findOrFail($validated['machine_id']);
            $machine->update([
                'threshold_warning' => $validated['warning'],
                'threshold_critical' => $validated['critical'],
                'motor_power_hp' => $validated['motor_power_hp'] ?? $machine->motor_power_hp,
                'motor_rpm' => $validated['motor_rpm'] ?? $machine->motor_rpm,
                'iso_class' => $validated['iso_class'] ?? $machine->iso_class,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Threshold configuration updated for ' . $machine->name,
                'config' => [
                    'machine_id' => $machine->id,
                    'machine_name' => $machine->name,
                    'warning' => $machine->threshold_warning,
                    'critical' => $machine->threshold_critical,
                    'motor_power_hp' => $machine->motor_power_hp,
                    'motor_rpm' => $machine->motor_rpm,
                    'iso_class' => $machine->iso_class,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating thresholds: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get threshold configuration for a specific machine
     */
    public function getMachineThresholds($machineId)
    {
        try {
            $machine = Machine::findOrFail($machineId);

            return response()->json([
                'success' => true,
                'config' => [
                    'machine_id' => $machine->id,
                    'machine_name' => $machine->name,
                    'warning' => (float) ($machine->threshold_warning ?? 21.84),
                    'critical' => (float) ($machine->threshold_critical ?? 25.11),
                    'motor_power_hp' => $machine->motor_power_hp,
                    'motor_rpm' => $machine->motor_rpm,
                    'iso_class' => $machine->iso_class ?? 'Class II',
                ],
                'threshold_reference' => self::THRESHOLD_PRESETS,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching machine thresholds: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply class preset to a machine
     */
    public function applyIsoPreset(Request $request)
    {
        try {
            $validated = $request->validate([
                'machine_id' => 'required|exists:machines,id',
                'iso_class' => 'required|string|in:Class I,Class II,Class III,Class IV',
            ]);

            $machine = Machine::findOrFail($validated['machine_id']);
            $preset = self::THRESHOLD_PRESETS[$validated['iso_class']];

            $machine->update([
                'threshold_warning' => $preset['warning'],
                'threshold_critical' => $preset['critical'],
                'iso_class' => $validated['iso_class'],
            ]);

            return response()->json([
                'success' => true,
                'message' => "Applied preset {$validated['iso_class']} to {$machine->name}",
                'config' => [
                    'machine_id' => $machine->id,
                    'machine_name' => $machine->name,
                    'warning' => $machine->threshold_warning,
                    'critical' => $machine->threshold_critical,
                    'iso_class' => $machine->iso_class,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error applying preset: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all machines with their threshold configs
     */
    public function getAllMachineThresholds()
    {
        try {
            $machines = Machine::all()->map(function ($machine) {
                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'location' => $machine->location,
                    'threshold_warning' => (float) ($machine->threshold_warning ?? 21.84),
                    'threshold_critical' => (float) ($machine->threshold_critical ?? 25.11),
                    'motor_power_hp' => $machine->motor_power_hp,
                    'motor_rpm' => $machine->motor_rpm,
                    'iso_class' => $machine->iso_class ?? 'Class II',
                ];
            });

            return response()->json([
                'success' => true,
                'machines' => $machines,
                'threshold_reference' => self::THRESHOLD_PRESETS,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching machine thresholds: ' . $e->getMessage(),
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

            $alerts = $query->paginate($request->get('per_page', 20));

            $alerts->getCollection()->transform(function ($alert) {
                // Use per-machine threshold
                $thresholds = $alert->machine
                    ? $this->getMachineThreshold($alert->machine)
                    : $this->getDefaultThreshold();

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
                    'resolved_by' => Cache::get("alert_resolved_by_{$alert->id}"),
                    'resolution_notes' => Cache::get("alert_resolution_notes_{$alert->id}", ''),
                    'threshold_warning' => $thresholds['warning'],
                    'threshold_critical' => $thresholds['critical'],
                ];
            });

            // Show acknowledged or resolved alerts in history
            $alerts->setCollection(
                $alerts->getCollection()
                    ->filter(fn($alert) => !empty($alert['resolved']) || !empty($alert['acknowledged']))
                    ->values()
            );

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

            $alerts = $query->get();

            $csvData = "ID,Machine,Location,RMS (mm/s),Severity,Status,Timestamp,Threshold Warning,Threshold Critical,Acknowledged,Acknowledged By,Acknowledged At\n";

            foreach ($alerts as $alert) {
                // Use per-machine threshold
                $thresholds = $alert->machine
                    ? $this->getMachineThreshold($alert->machine)
                    : $this->getDefaultThreshold();

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
                    $thresholds['warning'],
                    $thresholds['critical'],
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
