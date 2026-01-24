<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\TemperatureReading;
use App\Models\RawSample; // gunakan model raw_samples
use App\Models\RawBatch;

class ESPController extends Controller
{
    public function receiveData(Request $request)
    {
        $data = $request->all();
        Log::info('Data diterima dari ESP:', $data);

        try {
            // Simpan info batch ke raw_batches
            $batch = null;
            if (isset($data['machine_id']) && isset($data['captured_at'])) {
                // Convert captured_at to MySQL-compatible format
                $capturedAt = isset($data['captured_at']) ? \Carbon\Carbon::parse($data['captured_at'])->format('Y-m-d H:i:s') : null;

                // Provide a default value for 'name' if not set
                $name = 'ESP32 Batch ' . now()->format('Ymd_His');

                $batch = RawBatch::create([
                    'machine_id'  => $data['machine_id'],
                    'captured_at' => $capturedAt,
                    'name'        => $name,
                    'batch_time'  => $capturedAt ?? now(),
                ]);
            }

            $baseTime = \Carbon\Carbon::parse($data['captured_at'] ?? now());
            // Simpan data detail ke raw_samples
            if ($batch && isset($data['data']) && is_array($data['data'])) {
                $rows = [];
                foreach ($data['data'] as $item) {
                    // $item: [t_ms, ax_g, ay_g, az_g, value?]
                    $t_ms = isset($item[0]) ? (int)$item[0] : null;
                    $ax_g = isset($item[1]) ? $item[1] : null;
                    $ay_g = isset($item[2]) ? $item[2] : null;
                    $az_g = isset($item[3]) ? $item[3] : null;
                    $value = isset($item[4]) ? $item[4] : null;
                    $capturedAtRaw = $data['captured_at'] ?? null;
                    $capturedAt = null;
                    if ($capturedAtRaw) {
                        try {
                            $capturedAt = \Carbon\Carbon::parse($capturedAtRaw)->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $capturedAt = null;
                        }
                    }
                    $rows[] = [
                        'machine_id'    => $data['machine_id'] ?? null,
                        'raw_batch_id'  => $batch->id,
                        't_ms'          => $t_ms,
                        'ax_g'          => $ax_g,
                        'ay_g'          => $ay_g,
                        'az_g'          => $az_g,
                        'value'         => $value,
                        'temperature_c' => $data['temperature_c'] ?? null,
                        'captured_at'   => $capturedAt,
                        'sample_time'   => $t_ms !== null ? $baseTime->copy()->addMilliseconds($t_ms) : $baseTime,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
                \App\Models\RawSample::insert($rows);
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
