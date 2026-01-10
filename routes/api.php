<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GrafikRmsController;

Route::get('/grafik-rms', [GrafikRmsController::class, 'index']);
