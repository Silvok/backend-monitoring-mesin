<?php

namespace App\Http\Controllers;

class AnalisisController extends Controller
{
    /**
     * Proses FFT otomatis: ambil data mentah, jalankan script Python, dan simpan hasil ke backend
     */
    public function prosesFFT($analysisResultId)
    {
        // 1. Ambil data mentah dari raw_samples
        $samples = \DB::table('raw_samples')
            ->where('analysis_result_id', $analysisResultId)
            ->orderBy('id')
            ->pluck('value')
            ->toArray();

        if (empty($samples)) {
            return response()->json(['success' => false, 'message' => 'Data mentah tidak ditemukan']);
        }

        // 2. Simpan ke file sementara
        $filePath = storage_path('app/fft_input_' . $analysisResultId . '.csv');
        file_put_contents($filePath, implode(',', $samples));

        // 3. Jalankan script Python
        $python = 'python'; // atau path ke python.exe
        $script = base_path('fft_process.py'); // letakkan script Python di root project
        $cmd = "$python $script $filePath $analysisResultId";
        $output = shell_exec($cmd);

        return response()->json(['success' => true, 'output' => $output]);
    }
}
