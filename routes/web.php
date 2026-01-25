<?php

// API endpoint for user detail (for edit modal)
Route::get('/api/users/{id}', [App\Http\Controllers\UserManagementController::class, 'show'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('api.users.show');

// User Management Page
use App\Http\Controllers\UserManagementController;

Route::get('/user-management', [UserManagementController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('user-management');
Route::post('/user-management', [UserManagementController::class, 'store'])->middleware(['auth', 'verified', 'role:admin'])->name('user-management.store');
Route::put('/user-management/{id}', [UserManagementController::class, 'update'])->middleware(['auth', 'verified', 'role:admin'])->name('user-management.update');
Route::delete('/user-management/{id}', [UserManagementController::class, 'destroy'])->middleware(['auth', 'verified', 'role:admin'])->name('user-management.destroy');
Route::post('/user-management/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->middleware(['auth', 'verified', 'role:admin'])->name('user-management.reset-password');

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
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('dashboard');

Route::get('/real-time-sensor', [DashboardController::class, 'realTimeSensor'])
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('real-time-sensor');

Route::get('/data-grafik', [DashboardController::class, 'dataGrafik'])
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('data-grafik');

Route::get('/monitoring-mesin', [\App\Http\Controllers\MonitoringController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('monitoring-mesin');

Route::get('/parameter-monitoring', [ParameterMonitoringController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('parameter-monitoring');

Route::get('/pengaturan', [SettingsController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('settings');

Route::get('/api/monitoring/data', [\App\Http\Controllers\MonitoringController::class, 'getMonitoringData'])
    ->middleware(['auth']);

Route::get('/api/monitoring/trend', [\App\Http\Controllers\MonitoringController::class, 'getTrendData'])
    ->middleware(['auth']);

Route::get('/api/monitoring/trend-analysis', [\App\Http\Controllers\MonitoringController::class, 'getTrendAnalysis'])
    ->middleware(['auth']);

Route::get('/analisis', [\App\Http\Controllers\AnalisisController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('analisis');

// Alert Management Routes
Route::get('/alert-management', [AlertManagementController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin,teknisi'])
    ->name('alert-management');

// Route untuk trigger proses FFT otomatis (bisa dipanggil dari browser/postman)
Route::post('/proses-fft/{analysisResultId}', [\App\Http\Controllers\AnalisisController::class, 'prosesFFT'])
    ->middleware(['auth', 'verified']);

// API Routes for real-time updates
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware('role:admin,teknisi');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->middleware('role:admin,teknisi');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->middleware('role:admin,teknisi');

    Route::get('/api/dashboard-data', [DashboardApiController::class, 'getDashboardData'])->middleware('role:admin,teknisi');
    Route::get('/api/machine-status', [DashboardApiController::class, 'getMachineStatus'])->middleware('role:admin,teknisi');
    Route::get('/api/top-machines-by-risk', [DashboardApiController::class, 'getTopMachinesByRisk'])->middleware('role:admin,teknisi');
    Route::get('/api/machine/{id}/sensor-data', [DashboardApiController::class, 'getMachineSensorData'])->middleware('role:admin,teknisi');
    Route::get('/api/machine/{id}/historical-data', [DashboardApiController::class, 'getHistoricalData'])->middleware('role:admin,teknisi');
    Route::get('/api/machine/{id}/historical-trend', [DashboardApiController::class, 'getHistoricalTrend'])->middleware('role:admin,teknisi');
    Route::get('/api/machine/{id}/alerts', [DashboardController::class, 'getMachineAlerts'])->middleware('role:admin,teknisi');
    Route::post('/api/analysis', [DashboardApiController::class, 'getAnalysisData'])->middleware('role:admin,teknisi');

    // FFT API route
    Route::post('/api/fft-result', [DashboardController::class, 'storeFFT'])->middleware('role:admin');

    // FFT Spectrum API routes
    Route::get('/api/fft/latest', [FFTController::class, 'getLatestFFT'])->middleware('role:admin,teknisi');
    Route::get('/api/fft/history', [FFTController::class, 'getFFTHistory'])->middleware('role:admin,teknisi');
    Route::get('/api/fft/spectrum', [FFTController::class, 'getFFTSpectrum'])->middleware('role:admin,teknisi');

    // Alert API routes (existing)
    Route::get('/api/alerts', [AlertController::class, 'getActiveAlerts'])->middleware('role:admin,teknisi');
    Route::post('/api/alerts/{id}/acknowledge', [AlertController::class, 'acknowledgeAlert'])->middleware('role:admin,teknisi');
    Route::post('/api/alerts/machine/{machineId}/dismiss', [AlertController::class, 'dismissMachineAlerts'])->middleware('role:admin');
    Route::get('/api/alerts/stats', [AlertController::class, 'getAlertStats'])->middleware('role:admin,teknisi');

    // Alert Management API routes
    Route::get('/api/alert-management/alerts', [AlertManagementController::class, 'getAlerts'])->middleware('role:admin,teknisi');
    Route::get('/api/alert-management/stats', [AlertManagementController::class, 'getStats'])->middleware('role:admin,teknisi');
    Route::post('/api/alert-management/alerts/{id}/acknowledge', [AlertManagementController::class, 'acknowledgeAlert'])->middleware('role:admin,teknisi');
    Route::post('/api/alert-management/alerts/{id}/resolve', [AlertManagementController::class, 'resolveAlert'])->middleware('role:admin');
    Route::post('/api/alert-management/alerts/bulk-acknowledge', [AlertManagementController::class, 'bulkAcknowledge'])->middleware('role:admin');
    Route::post('/api/alert-management/thresholds', [AlertManagementController::class, 'updateThresholds'])->middleware('role:admin');
    Route::post('/api/alert-management/notifications', [AlertManagementController::class, 'updateNotifications'])->middleware('role:admin');
    Route::get('/api/alert-management/history', [AlertManagementController::class, 'getHistory'])->middleware('role:admin,teknisi');
    Route::get('/api/alert-management/export', [AlertManagementController::class, 'exportAlerts'])->middleware('role:admin');

    // Per-machine threshold API routes
    Route::get('/api/alert-management/machine-thresholds', [AlertManagementController::class, 'getAllMachineThresholds'])->middleware('role:admin');
    Route::get('/api/alert-management/machine-thresholds/{machineId}', [AlertManagementController::class, 'getMachineThresholds'])->middleware('role:admin');
    Route::post('/api/alert-management/apply-iso-preset', [AlertManagementController::class, 'applyIsoPreset'])->middleware('role:admin');
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
