<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        $machines = Machine::orderBy('name')->get();

        // Data for initial load or defaults can be added here
        return view('pages.monitoring-mesin', compact('machines'));
    }
}
