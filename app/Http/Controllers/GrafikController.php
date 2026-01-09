<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawSample;
use Carbon\Carbon;

class GrafikController extends Controller
{
    public function getRmsData(Request $request)
    {
        $machineId = $request->input('machine_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validasi input
        if (!$machineId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak lengkap',
            ], 400);
        }

        $samples = RawSample::where('machine_id', $machineId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        $labels = $samples->pluck('created_at')->map(function($date) {
            return Carbon::parse($date)->format('d-m-Y H:i');
        })->toArray();

        // Ganti 'ax_g' dengan field RMS Value jika ada field khusus
        $values = $samples->pluck('ax_g')->toArray();

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
