<?php

// API endpoint for user detail (for edit modal)
Route::get('/api/users/{id}', [App\Http\Controllers\UserManagementController::class, 'show'])
    ->middleware(['auth', 'verified', 'permission:user_management.view'])
    ->name('api.users.show');

// User Management Page
use App\Http\Controllers\UserManagementController;

Route::get('/user-management', [UserManagementController::class, 'index'])->middleware(['auth', 'verified', 'permission:user_management.view'])->name('user-management');
Route::post('/user-management', [UserManagementController::class, 'store'])->middleware(['auth', 'verified', 'permission:user_management.create'])->name('user-management.store');
Route::put('/user-management/{id}', [UserManagementController::class, 'update'])->middleware(['auth', 'verified', 'permission:user_management.edit'])->name('user-management.update');
Route::delete('/user-management/{id}', [UserManagementController::class, 'destroy'])->middleware(['auth', 'verified', 'permission:user_management.delete'])->name('user-management.destroy');
Route::post('/user-management/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->middleware(['auth', 'verified', 'permission:user_management.reset_password'])->name('user-management.reset-password');

Route::get('/role-management', [App\Http\Controllers\RoleManagementController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:roles.view'])
    ->name('role-management');

Route::post('/role-management', [App\Http\Controllers\RoleManagementController::class, 'store'])
    ->middleware(['auth', 'verified', 'permission:roles.create'])
    ->name('role-management.store');

Route::put('/role-management/{role}', [App\Http\Controllers\RoleManagementController::class, 'update'])
    ->middleware(['auth', 'verified', 'permission:roles.edit'])
    ->name('role-management.update');

Route::delete('/role-management/{role}', [App\Http\Controllers\RoleManagementController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'permission:roles.delete'])
    ->name('role-management.destroy');

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\FFTController;
use App\Http\Controllers\AlertManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ParameterMonitoringController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:dashboard.view'])
    ->name('dashboard');

Route::get('/real-time-sensor', [DashboardController::class, 'realTimeSensor'])
    ->middleware(['auth', 'verified', 'permission:realtime.view'])
    ->name('real-time-sensor');

Route::get('/data-grafik', [DashboardController::class, 'dataGrafik'])
    ->middleware(['auth', 'verified', 'permission:reports.view'])
    ->name('data-grafik');

Route::get('/laporan-bulanan', [\App\Http\Controllers\MonthlyReportController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:reports.view'])
    ->name('monthly-report');

Route::get('/laporan-bulanan/export', [\App\Http\Controllers\MonthlyReportController::class, 'exportCsv'])
    ->middleware(['auth', 'verified', 'permission:reports.view'])
    ->name('monthly-report.export');

Route::post('/laporan-bulanan/pdf', [\App\Http\Controllers\MonthlyReportController::class, 'exportPdf'])
    ->middleware(['auth', 'verified', 'permission:reports.view'])
    ->name('monthly-report.pdf');

Route::get('/monitoring-mesin', [\App\Http\Controllers\MonitoringController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:monitoring.view'])
    ->name('monitoring-mesin');

Route::get('/parameter-monitoring', [ParameterMonitoringController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:parameter.view'])
    ->name('parameter-monitoring');

Route::get('/pengaturan', [SettingsController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:settings.view'])
    ->name('settings');

Route::get('/api/monitoring/data', [\App\Http\Controllers\MonitoringController::class, 'getMonitoringData'])
    ->middleware(['auth']);

Route::get('/api/monitoring/trend', [\App\Http\Controllers\MonitoringController::class, 'getTrendData'])
    ->middleware(['auth']);

Route::get('/api/monitoring/trend-analysis', [\App\Http\Controllers\MonitoringController::class, 'getTrendAnalysis'])
    ->middleware(['auth']);


// Alert Management Routes
Route::get('/alert-management', [AlertManagementController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:alert_management.view'])
    ->name('alert-management');

// Route untuk trigger proses FFT otomatis (bisa dipanggil dari browser/postman)
Route::post('/proses-fft/{analysisResultId}', [\App\Http\Controllers\AnalisisController::class, 'prosesFFT'])
    ->middleware(['auth', 'verified']);

// API Routes for real-time updates
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware('permission:alerts.view');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->middleware('permission:alerts.view');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->middleware('permission:alerts.view');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->middleware('permission:alerts.view');

    Route::get('/api/dashboard-data', [DashboardApiController::class, 'getDashboardData'])->middleware('permission:dashboard.view');
    Route::get('/api/machine-status', [DashboardApiController::class, 'getMachineStatus'])->middleware('permission:dashboard.view');
    Route::get('/api/top-machines-by-risk', [DashboardApiController::class, 'getTopMachinesByRisk'])->middleware('permission:dashboard.view');
    Route::get('/api/machine/{id}/sensor-data', [DashboardApiController::class, 'getMachineSensorData'])->middleware('permission:monitoring.view');
    Route::get('/api/machine/{id}/historical-data', [DashboardApiController::class, 'getHistoricalData'])->middleware('permission:monitoring.view');
    Route::get('/api/machine/{id}/historical-trend', [DashboardApiController::class, 'getHistoricalTrend'])->middleware('permission:monitoring.view');
    Route::get('/api/machine/{id}/alerts', [DashboardController::class, 'getMachineAlerts'])->middleware('permission:alerts.view');
    Route::post('/api/analysis', [DashboardApiController::class, 'getAnalysisData'])->middleware('permission:analysis.view');

    // Settings API routes
    Route::post('/api/settings/sampling-interval', [SettingsController::class, 'updateSamplingInterval'])->middleware('permission:settings.update');

    // FFT API route
    Route::post('/api/fft-result', [DashboardController::class, 'storeFFT'])->middleware('permission:fft.store');

    // FFT Spectrum API routes
    Route::get('/api/fft/latest', [FFTController::class, 'getLatestFFT'])->middleware('permission:fft.view');
    Route::get('/api/fft/history', [FFTController::class, 'getFFTHistory'])->middleware('permission:fft.view');
    Route::get('/api/fft/spectrum', [FFTController::class, 'getFFTSpectrum'])->middleware('permission:fft.view');

    // Alert API routes (existing)
    Route::get('/api/alerts', [AlertController::class, 'getActiveAlerts'])->middleware('permission:alerts.view');
    Route::post('/api/alerts/{id}/acknowledge', [AlertController::class, 'acknowledgeAlert'])->middleware('permission:alerts.ack');
    Route::post('/api/alerts/machine/{machineId}/dismiss', [AlertController::class, 'dismissMachineAlerts'])->middleware('permission:alert_management.resolve');
    Route::get('/api/alerts/stats', [AlertController::class, 'getAlertStats'])->middleware('permission:alerts.view');

    // Alert Management API routes
    Route::get('/api/alert-management/alerts', [AlertManagementController::class, 'getAlerts'])->middleware('permission:alert_management.view');
    Route::get('/api/alert-management/stats', [AlertManagementController::class, 'getStats'])->middleware('permission:alert_management.stats');
    Route::post('/api/alert-management/alerts/{id}/acknowledge', [AlertManagementController::class, 'acknowledgeAlert'])->middleware('permission:alerts.ack');
    Route::post('/api/alert-management/alerts/{id}/resolve', [AlertManagementController::class, 'resolveAlert'])->middleware('permission:alert_management.resolve');
    Route::post('/api/alert-management/alerts/bulk-acknowledge', [AlertManagementController::class, 'bulkAcknowledge'])->middleware('permission:alert_management.bulk_ack');
    Route::post('/api/alert-management/thresholds', [AlertManagementController::class, 'updateThresholds'])->middleware('permission:alert_management.thresholds');
    Route::post('/api/alert-management/notifications', [AlertManagementController::class, 'updateNotifications'])->middleware('permission:alert_management.notifications');
    Route::get('/api/alert-management/history', [AlertManagementController::class, 'getHistory'])->middleware('permission:alert_management.history');
    Route::get('/api/alert-management/export', [AlertManagementController::class, 'exportAlerts'])->middleware('permission:export.alerts');

    // Per-machine threshold API routes
    Route::get('/api/alert-management/machine-thresholds', [AlertManagementController::class, 'getAllMachineThresholds'])->middleware('permission:alert_management.thresholds');
    Route::get('/api/alert-management/machine-thresholds/{machineId}', [AlertManagementController::class, 'getMachineThresholds'])->middleware('permission:alert_management.thresholds');
    Route::post('/api/alert-management/apply-iso-preset', [AlertManagementController::class, 'applyIsoPreset'])->middleware('permission:alert_management.thresholds');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route monitoring dari predictive-api
Route::get('/check-data', function () {
    $batches = DB::table('raw_batches')->count();
    $samples = DB::table('raw_samples')->count();
    $analysis = DB::table('analysis_results')->count();
    $lastBatch = DB::table('raw_batches')->orderBy('id', 'desc')->first();
    $lastAnalysis = DB::table('analysis_results')->orderBy('id', 'desc')->first();
    return [
        'status' => 'success',
        'data' => [
            'total_batches' => $batches,
            'total_samples' => $samples,
            'total_analysis_results' => $analysis,
            'last_batch' => $lastBatch,
            'last_analysis' => $lastAnalysis,
        ]
    ];
});

require __DIR__ . '/auth.php';
