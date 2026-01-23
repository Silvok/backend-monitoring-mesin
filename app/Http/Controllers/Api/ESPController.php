<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\TemperatureReading;
use App\Models\RawSample;

class ESPController extends Controller
{
    public function receiveData(Request $request)
    {
        $data = $request->all();
        Log::info('Data diterima dari ESP:', $data);

        try {
            // Simpan data batch (array) ke tabel raw_samples
            if (isset($data['data']) && is_array($data['data'])) {
                $batchId = $data['captured_at'] ?? now(); // gunakan captured_at sebagai batch_id unik
                foreach ($data['data'] as $item) {
                    RawSample::create([
                        'machine_id'    => $data['machine_id'] ?? null,
                        'batch_id'      => $batchId,
                        't_ms'          => isset($item['timestamp']) ? strtotime($item['timestamp']) * 1000 : null,
                        'ax_g'          => $item['accel_x'] ?? null,
                        'ay_g'          => $item['accel_y'] ?? null,
                        'az_g'          => $item['accel_z'] ?? null,
                        // tambahkan field lain jika ada di tabel
                    ]);
                }
            }

            // Simpan data suhu jika ada
            if (isset($data['temperature_c'])) {
                TemperatureReading::create([
                    'machine_id'    => $data['machine_id'] ?? null,
                    'recorded_at'   => $data['captured_at'] ?? now(),
                    'temperature_c' => $data['temperature_c'],
                ]);
            }

            return response()->json(['status' => 'success', 'received' => $data], 200);
        } catch (\Exception $e) {
            Log::error('Gagal simpan data batch ESP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'data' => $data]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
