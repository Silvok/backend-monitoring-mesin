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

// API Routes for real-time updates
Route::middleware('auth')->group(function () {
    Route::get('/api/dashboard-data', [DashboardApiController::class, 'getDashboardData']);
    Route::get('/api/machine-status', [DashboardApiController::class, 'getMachineStatus']);
    Route::get('/api/top-machines-by-risk', [DashboardApiController::class, 'getTopMachinesByRisk']);

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
