<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\TemperatureReading;
use App\Models\Machine;
use App\Events\SensorUpdated;

class ESPController extends Controller
{
    public function receiveData(Request $request)
    {
        $data = $request->all();
        Log::info('PAYLOAD DEBUG ESP:', $data);
        Log::info('Data diterima dari ESP:', $data);

        try {
            $machineId = $data['machine_id'] ?? null;
            $capturedAtRaw = $data['captured_at'] ?? null;
            $capturedAt = null;
            if ($capturedAtRaw) {
                try {
                    $capturedAt = \Carbon\Carbon::parse($capturedAtRaw)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $capturedAt = null;
                }
            }

            if ($machineId) {
                Machine::firstOrCreate(
                    ['id' => $machineId],
                    ['name' => 'Machine ' . $machineId]
                );
            }

            $isSamplePayload = (isset($data['data']) && is_array($data['data']))
                || (isset($data['axis']) && isset($data['samples']) && is_array($data['samples']));

            $rawSampleColumns = Schema::hasTable('raw_samples')
                ? array_flip(Schema::getColumnListing('raw_samples'))
                : [];
            $rawBatchColumns = Schema::hasTable('raw_batches')
                ? array_flip(Schema::getColumnListing('raw_batches'))
                : [];

            // Simpan info batch ke raw_batches hanya untuk payload sample
            $batchId = null;
            if ($isSamplePayload && $machineId && $capturedAt && !empty($rawBatchColumns)) {
                $batchData = [
                    'machine_id' => $machineId,
                    'captured_at' => $capturedAt,
                    'name' => 'ESP32 Batch ' . now()->format('Ymd_His'),
                    'batch_time' => $capturedAt ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $batchData = array_intersect_key($batchData, $rawBatchColumns);
                if (!empty($batchData)) {
                    $batchId = DB::table('raw_batches')->insertGetId($batchData);
                }
            }

            $baseTime = \Carbon\Carbon::parse($capturedAtRaw ?? now());
            $temperatureC = $data['temperature_c'] ?? $data['temperature'] ?? null;

            // Simpan data detail ke raw_samples
            if ($isSamplePayload && $machineId && !empty($rawSampleColumns)) {
                $rows = [];
                $addIf = function (array &$row, string $key, $value) use ($rawSampleColumns) {
                    if (array_key_exists($key, $rawSampleColumns)) {
                        $row[$key] = $value;
                    }
                };

                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $item) {
                        // $item: [t_ms, ax_g, ay_g, az_g, value?]
                        $t_ms = isset($item[0]) ? (int) $item[0] : null;
                        $ax_g = isset($item[1]) ? $item[1] : null;
                        $ay_g = isset($item[2]) ? $item[2] : null;
                        $az_g = isset($item[3]) ? $item[3] : null;
                        $value = isset($item[4]) ? $item[4] : null;

                        $row = [];
                        $addIf($row, 'machine_id', $machineId);
                        $addIf($row, 'raw_batch_id', $batchId);
                        $addIf($row, 'batch_id', $batchId);
                        $addIf($row, 't_ms', $t_ms);
                        $addIf($row, 'ax_g', $ax_g);
                        $addIf($row, 'ay_g', $ay_g);
                        $addIf($row, 'az_g', $az_g);
                        $addIf($row, 'value', $value);
                        $addIf($row, 'temperature_c', $temperatureC);
                        $addIf($row, 'captured_at', $capturedAt);
                        $addIf($row, 'sample_time', $t_ms !== null ? $baseTime->copy()->addMilliseconds($t_ms) : $baseTime);
                        $addIf($row, 'name', $batchId ? 'ESP32 Sample ' . $batchId : 'ESP32 Sample');
                        $addIf($row, 'created_at', now());
                        $addIf($row, 'updated_at', now());

                        if (!empty($row)) {
                            $rows[] = $row;
                        }
                    }
                } else {
                    $axis = strtoupper((string) ($data['axis'] ?? ''));
                    foreach ($data['samples'] as $idx => $sampleValue) {
                        $t_ms = is_numeric($idx) ? (int) $idx : null;
                        $ax_g = $axis === 'X' ? $sampleValue : null;
                        $ay_g = $axis === 'Y' ? $sampleValue : null;
                        $az_g = $axis === 'Z' ? $sampleValue : null;

                        if ($az_g === null && array_key_exists('az_g', $rawSampleColumns)) {
                            $az_g = 0;
                        }

                        $row = [];
                        $addIf($row, 'machine_id', $machineId);
                        $addIf($row, 'raw_batch_id', $batchId);
                        $addIf($row, 'batch_id', $batchId);
                        $addIf($row, 't_ms', $t_ms);
                        $addIf($row, 'ax_g', $ax_g);
                        $addIf($row, 'ay_g', $ay_g);
                        $addIf($row, 'az_g', $az_g);
                        $addIf($row, 'value', is_numeric($sampleValue) ? (float) $sampleValue : $sampleValue);
                        $addIf($row, 'temperature_c', $temperatureC);
                        $addIf($row, 'captured_at', $capturedAt);
                        $addIf($row, 'sample_time', $t_ms !== null ? $baseTime->copy()->addMilliseconds($t_ms) : $baseTime);
                        $addIf($row, 'name', $batchId ? 'ESP32 Sample ' . $batchId : 'ESP32 Sample');
                        $addIf($row, 'created_at', now());
                        $addIf($row, 'updated_at', now());

                        if (!empty($row)) {
                            $rows[] = $row;
                        }
                    }
                }

                if (!empty($rows)) {
                    DB::table('raw_samples')->insert($rows);

                    if ($machineId) {
                        $lastRow = end($rows);
                        $timestampRaw = $lastRow['sample_time'] ?? $capturedAt ?? now();
                        if ($timestampRaw instanceof \Carbon\CarbonInterface) {
                            $timestamp = $timestampRaw->toIso8601String();
                        } else {
                            $timestamp = \Carbon\Carbon::parse($timestampRaw)->toIso8601String();
                        }

                        $ax = array_key_exists('ax_g', $lastRow) ? (float) $lastRow['ax_g'] : null;
                        $ay = array_key_exists('ay_g', $lastRow) ? (float) $lastRow['ay_g'] : null;
                        $az = array_key_exists('az_g', $lastRow) ? (float) $lastRow['az_g'] : null;
                        $temp = array_key_exists('temperature_c', $lastRow)
                            ? (float) $lastRow['temperature_c']
                            : ($temperatureC !== null ? (float) $temperatureC : null);

                        broadcast(new SensorUpdated(
                            (int) $machineId,
                            $ax,
                            $ay,
                            $az,
                            $temp,
                            $timestamp
                        ));
                    }
                }
            }

            // Simpan data suhu jika ada
            if ($temperatureC !== null) {
                TemperatureReading::create([
                    'machine_id'    => $machineId,
                    'recorded_at'   => $data['captured_at'] ?? now(),
                    'value'         => $temperatureC,
                    'temperature_c' => $temperatureC,
                ]);
            }

            // Dispatch analysis job jika batch berhasil dibuat
            if ($batchId) {
                \App\Jobs\AnalyzeBatchJob::dispatch($batchId);
            }

            return response()->json(['status' => 'success', 'received' => $data], 200);
        } catch (\Exception $e) {
            Log::error('Gagal simpan data batch ESP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'data' => $data]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
