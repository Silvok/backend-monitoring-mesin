<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;

class AnalisisController extends Controller
{
    public function index()
    {
        $totalMachines = Machine::count();
        $statusCounts = AnalysisResult::select('condition_status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('condition_status')
            ->pluck('count', 'condition_status');

        $lastUpdate = AnalysisResult::latest()->first()?->updated_at;

        return view('pages.analisis', compact('totalMachines', 'statusCounts', 'lastUpdate'));
    }
}
