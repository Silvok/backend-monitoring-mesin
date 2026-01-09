<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrafikController;

Route::get('/grafik-rms', [GrafikController::class, 'getRmsData']);
