<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\Api\FFTController;

use App\Http\Controllers\Api\ESPController;

Route::get('/grafik-rms', [GrafikController::class, 'getRmsData']);

// FFT API Routes
Route::prefix('fft')->group(function () {
    Route::get('/latest', [FFTController::class, 'getLatestFFT']);
    Route::get('/history', [FFTController::class, 'getFFTHistory']);
    Route::get('/spectrum', [FFTController::class, 'getFFTSpectrum']);
});

// Endpoint untuk menerima data dari ESP
Route::post('/esp-data', [ESPController::class, 'receiveData']);

// Endpoint agar ESP bisa mengirim ke /api/v1/sensor/batch dan /api/v1/sensor/temperature
Route::post('/v1/sensor/batch', [ESPController::class, 'receiveData']);
Route::post('/v1/sensor/temperature', [ESPController::class, 'receiveData']);
