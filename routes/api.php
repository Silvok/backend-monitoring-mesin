<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\Api\FFTController;

Route::get('/grafik-rms', [GrafikController::class, 'getRmsData']);

// FFT API Routes
Route::prefix('fft')->group(function () {
    Route::get('/latest', [FFTController::class, 'getLatestFFT']);
    Route::get('/history', [FFTController::class, 'getFFTHistory']);
    Route::get('/spectrum', [FFTController::class, 'getFFTSpectrum']);
});
