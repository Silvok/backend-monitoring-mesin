<?php

// API endpoint for user detail (for edit modal)
Route::get('/api/users/{id}', [App\Http\Controllers\UserManagementController::class, 'show'])->name('api.users.show');

// User Management Page
use App\Http\Controllers\UserManagementController;

Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management');
Route::post('/user-management', [UserManagementController::class, 'store'])->name('user-management.store');
Route::put('/user-management/{id}', [UserManagementController::class, 'update'])->name('user-management.update');
Route::delete('/user-management/{id}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
Route::post('/user-management/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->name('user-management.reset-password');

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\FFTController;
use App\Http\Controllers\AlertManagementController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/real-time-sensor', [DashboardController::class, 'realTimeSensor'])
    ->middleware(['auth', 'verified'])
    ->name('real-time-sensor');

Route::get('/data-grafik', [DashboardController::class, 'dataGrafik'])
    ->middleware(['auth', 'verified'])
    ->name('data-grafik');

Route::get('/monitoring-mesin', [\App\Http\Controllers\MonitoringController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('monitoring-mesin');

Route::get('/api/monitoring/data', [\App\Http\Controllers\MonitoringController::class, 'getMonitoringData'])
    ->middleware(['auth']);

Route::get('/api/monitoring/trend', [\App\Http\Controllers\MonitoringController::class, 'getTrendData'])
    ->middleware(['auth']);

Route::get('/api/monitoring/trend-analysis', [\App\Http\Controllers\MonitoringController::class, 'getTrendAnalysis'])
    ->middleware(['auth']);

Route::get('/analisis', [\App\Http\Controllers\AnalisisController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('analisis');

// Alert Management Routes
Route::get('/alert-management', [AlertManagementController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('alert-management');

// Route untuk trigger proses FFT otomatis (bisa dipanggil dari browser/postman)
Route::post('/proses-fft/{analysisResultId}', [\App\Http\Controllers\AnalisisController::class, 'prosesFFT'])
    ->middleware(['auth', 'verified']);

// API Routes for real-time updates
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    Route::get('/api/dashboard-data', [DashboardApiController::class, 'getDashboardData']);
    Route::get('/api/machine-status', [DashboardApiController::class, 'getMachineStatus']);
    Route::get('/api/top-machines-by-risk', [DashboardApiController::class, 'getTopMachinesByRisk']);
    Route::get('/api/machine/{id}/sensor-data', [DashboardApiController::class, 'getMachineSensorData']);
    Route::get('/api/machine/{id}/historical-data', [DashboardApiController::class, 'getHistoricalData']);
    Route::get('/api/machine/{id}/historical-trend', [DashboardApiController::class, 'getHistoricalTrend']);
    Route::get('/api/machine/{id}/alerts', [DashboardController::class, 'getMachineAlerts']);
    Route::post('/api/analysis', [DashboardApiController::class, 'getAnalysisData']);

    // FFT API route
    Route::post('/api/fft-result', [DashboardController::class, 'storeFFT']);

    // FFT Spectrum API routes
    Route::get('/api/fft/latest', [FFTController::class, 'getLatestFFT']);
    Route::get('/api/fft/history', [FFTController::class, 'getFFTHistory']);
    Route::get('/api/fft/spectrum', [FFTController::class, 'getFFTSpectrum']);

    // Alert API routes (existing)
    Route::get('/api/alerts', [AlertController::class, 'getActiveAlerts']);
    Route::post('/api/alerts/{id}/acknowledge', [AlertController::class, 'acknowledgeAlert']);
    Route::post('/api/alerts/machine/{machineId}/dismiss', [AlertController::class, 'dismissMachineAlerts']);
    Route::get('/api/alerts/stats', [AlertController::class, 'getAlertStats']);

    // Alert Management API routes
    Route::get('/api/alert-management/alerts', [AlertManagementController::class, 'getAlerts']);
    Route::get('/api/alert-management/stats', [AlertManagementController::class, 'getStats']);
    Route::post('/api/alert-management/alerts/{id}/acknowledge', [AlertManagementController::class, 'acknowledgeAlert']);
    Route::post('/api/alert-management/alerts/{id}/resolve', [AlertManagementController::class, 'resolveAlert']);
    Route::post('/api/alert-management/alerts/bulk-acknowledge', [AlertManagementController::class, 'bulkAcknowledge']);
    Route::post('/api/alert-management/thresholds', [AlertManagementController::class, 'updateThresholds']);
    Route::post('/api/alert-management/notifications', [AlertManagementController::class, 'updateNotifications']);
    Route::get('/api/alert-management/history', [AlertManagementController::class, 'getHistory']);
    Route::get('/api/alert-management/export', [AlertManagementController::class, 'exportAlerts']);

    // Per-machine threshold API routes
    Route::get('/api/alert-management/machine-thresholds', [AlertManagementController::class, 'getAllMachineThresholds']);
    Route::get('/api/alert-management/machine-thresholds/{machineId}', [AlertManagementController::class, 'getMachineThresholds']);
    Route::post('/api/alert-management/apply-iso-preset', [AlertManagementController::class, 'applyIsoPreset']);
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
