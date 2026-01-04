<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\AlertController;
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

Route::get('/analisis', [DashboardController::class, 'analisis'])
    ->middleware(['auth', 'verified'])
    ->name('analisis');

// API Routes for real-time updates
Route::middleware('auth')->group(function () {
    Route::get('/api/dashboard-data', [DashboardApiController::class, 'getDashboardData']);
    Route::get('/api/machine-status', [DashboardApiController::class, 'getMachineStatus']);
    Route::get('/api/top-machines-by-risk', [DashboardApiController::class, 'getTopMachinesByRisk']);
    Route::get('/api/machine/{id}/sensor-data', [DashboardApiController::class, 'getMachineSensorData']);
    Route::get('/api/machine/{id}/historical-data', [DashboardApiController::class, 'getHistoricalData']);
    Route::get('/api/machine/{id}/historical-trend', [DashboardApiController::class, 'getHistoricalTrend']);
    Route::get('/api/machine/{id}/alerts', [DashboardController::class, 'getMachineAlerts']);
    Route::post('/api/analysis', [DashboardApiController::class, 'getAnalysisData']);

    // Alert API routes
    Route::get('/api/alerts', [AlertController::class, 'getActiveAlerts']);
    Route::post('/api/alerts/{id}/acknowledge', [AlertController::class, 'acknowledgeAlert']);
    Route::post('/api/alerts/machine/{machineId}/dismiss', [AlertController::class, 'dismissMachineAlerts']);
    Route::get('/api/alerts/stats', [AlertController::class, 'getAlertStats']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
