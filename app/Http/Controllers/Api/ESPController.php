<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

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

            $machine = null;
            if ($machineId) {
                $machine = Machine::firstOrCreate(
                    ['id' => $machineId],
                    ['name' => 'Machine ' . $machineId, 'is_active' => true]
                );
                if ($machine && !$machine->is_active) {
                    return response()->json([
                        'status' => 'ignored',
                        'reason' => 'machine_off',
                        'machine_id' => $machineId,
                    ], 202);
                }
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
            $precomputedMetrics = [];

            // Simpan data ringkasan 1 data per menit ke raw_samples
            if ($isSamplePayload && $machineId && !empty($rawSampleColumns)) {
                $addIf = function (array &$row, string $key, $value) use ($rawSampleColumns) {
                    if (array_key_exists($key, $rawSampleColumns)) {
                        $row[$key] = $value;
                    }
                };

                // Hitung rata-rata dari payload (agar 1 menit = 1 data ringkasan)
                $axSum = 0.0; $aySum = 0.0; $azSum = 0.0; $valSum = 0.0; $count = 0;
                $lastTms = null;
                $windowValues = [];
                $windowTimeValues = [];

                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $item) {
                        $t_ms = isset($item[0]) ? (int) $item[0] : null;
                        $ax_g = isset($item[1]) ? (float) $item[1] : 0.0;
                        $ay_g = isset($item[2]) ? (float) $item[2] : 0.0;
                        $az_g = isset($item[3]) ? (float) $item[3] : 0.0;
                        $value = isset($item[4]) ? (float) $item[4] : null;

                        $axSum += $ax_g;
                        $aySum += $ay_g;
                        $azSum += $az_g;
                        if ($value !== null) {
                            $valSum += $value;
                        }
                        $count++;
                        $lastTms = $t_ms;
                        $windowValues[] = sqrt(($ax_g * $ax_g) + ($ay_g * $ay_g) + ($az_g * $az_g));
                        $windowTimeValues[] = $t_ms;
                    }
                } else {
                    $axis = strtoupper((string) ($data['axis'] ?? ''));
                    foreach ($data['samples'] as $idx => $sampleValue) {
                        $t_ms = is_numeric($idx) ? (int) $idx : null;
                        $sampleValue = is_numeric($sampleValue) ? (float) $sampleValue : 0.0;
                        $ax_g = $axis === 'X' ? $sampleValue : 0.0;
                        $ay_g = $axis === 'Y' ? $sampleValue : 0.0;
                        $az_g = $axis === 'Z' ? $sampleValue : 0.0;

                        $axSum += $ax_g;
                        $aySum += $ay_g;
                        $azSum += $az_g;
                        $valSum += $sampleValue;
                        $count++;
                        $lastTms = $t_ms;
                        $windowValues[] = $sampleValue;
                        $windowTimeValues[] = $t_ms;
                    }
                }

                if ($count > 0) {
                    $precomputedMetrics = $this->buildPrecomputedMetricsFromWindow($windowValues, $windowTimeValues);

                    $avgAx = $axSum / $count;
                    $avgAy = $aySum / $count;
                    $avgAz = $azSum / $count;
                    $avgVal = $valSum > 0 ? ($valSum / $count) : null;

                    $intervalMinutes = (int) Cache::get('sampling_interval_minutes', 1);
                    if ($intervalMinutes < 1) {
                        $intervalMinutes = 1;
                    }
                    $minuteStart = $baseTime->copy()->second(0);
                    $bucketMinute = (int) (floor($minuteStart->minute / $intervalMinutes) * $intervalMinutes);
                    $minuteStart->minute($bucketMinute);
                    $minuteEnd = $minuteStart->copy()->addMinutes($intervalMinutes);

                    $alreadySaved = false;
                    if (array_key_exists('sample_time', $rawSampleColumns)) {
                        $alreadySaved = DB::table('raw_samples')
                            ->where('machine_id', $machineId)
                            ->whereBetween('sample_time', [$minuteStart, $minuteEnd])
                            ->exists();
                    }

                    if (!$alreadySaved) {
                        $row = [];
                        $addIf($row, 'machine_id', $machineId);
                        $addIf($row, 'raw_batch_id', $batchId);
                        $addIf($row, 'batch_id', $batchId);
                        $addIf($row, 't_ms', $lastTms);
                        $addIf($row, 'ax_g', $avgAx);
                        $addIf($row, 'ay_g', $avgAy);
                        $addIf($row, 'az_g', $avgAz);
                        $addIf($row, 'value', $avgVal);
                        $addIf($row, 'temperature_c', $temperatureC);
                        $addIf($row, 'captured_at', $capturedAt);
                        $addIf($row, 'sample_time', $minuteStart);
                        $addIf($row, 'name', $batchId ? 'ESP32 Sample ' . $batchId : 'ESP32 Sample');
                        $addIf($row, 'created_at', now());
                        $addIf($row, 'updated_at', now());

                        if (!empty($row)) {
                            DB::table('raw_samples')->insert($row);
                        }
                    }

                    if ($machineId) {
                        $timestamp = $minuteStart->toIso8601String();
                        broadcast(new SensorUpdated(
                            (int) $machineId,
                            $avgAx,
                            $avgAy,
                            $avgAz,
                            $temperatureC !== null ? (float) $temperatureC : null,
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
                \App\Jobs\AnalyzeBatchJob::dispatch($batchId, $precomputedMetrics);
            }

            return response()->json(['status' => 'success', 'received' => $data], 200);
        } catch (\Exception $e) {
            Log::error('Gagal simpan data batch ESP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'data' => $data]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function buildPrecomputedMetricsFromWindow(array $values, array $timeValues): array
    {
        $count = count($values);
        if ($count === 0) {
            return [];
        }

        $sum = array_sum($values);
        $mean = $sum / $count;
        $squareSum = 0.0;
        $peak = 0.0;
        foreach ($values as $v) {
            $squareSum += ($v * $v);
            $abs = abs($v);
            if ($abs > $peak) {
                $peak = $abs;
            }
        }
        $rms = sqrt($squareSum / $count);
        $variance = 0.0;
        foreach ($values as $v) {
            $variance += pow($v - $mean, 2);
        }
        $std = sqrt($variance / $count);

        $defaultFsHz = (float) env('SAMPLE_RATE_HZ', 1000);
        $dtSamples = [];
        for ($i = 1; $i < $count; $i++) {
            $currentT = $timeValues[$i] ?? null;
            $previousT = $timeValues[$i - 1] ?? null;
            if ($currentT !== null && $previousT !== null) {
                $dtMs = (float) $currentT - (float) $previousT;
                if ($dtMs > 0) {
                    $dtSamples[] = $dtMs / 1000;
                }
            }
        }
        $avgDt = !empty($dtSamples) ? (array_sum($dtSamples) / count($dtSamples)) : null;
        $fsHz = ($avgDt !== null && $avgDt > 0)
            ? (1 / $avgDt)
            : ($defaultFsHz > 0 ? $defaultFsHz : null);

        $dominantFreqHz = null;
        if ($count > 1 && $fsHz !== null && $fsHz > 0) {
            $signal = [];
            foreach ($values as $v) {
                $signal[] = $v - $mean;
            }

            $half = (int) floor($count / 2);
            $maxAmp = null;
            $maxIdx = null;
            for ($k = 1; $k <= $half; $k++) {
                $real = 0.0;
                $imag = 0.0;
                for ($nIdx = 0; $nIdx < $count; $nIdx++) {
                    $angle = 2 * M_PI * $k * $nIdx / $count;
                    $real += $signal[$nIdx] * cos($angle);
                    $imag -= $signal[$nIdx] * sin($angle);
                }
                $amp = sqrt(($real * $real) + ($imag * $imag)) / $count;
                if ($maxAmp === null || $amp > $maxAmp) {
                    $maxAmp = $amp;
                    $maxIdx = $k;
                }
            }

            if ($maxIdx !== null) {
                $dominantFreqHz = ($maxIdx * $fsHz) / $count;
            }
        }

        return [
            'sample_count' => $count,
            'rms_g' => round($rms, 6),
            'peak_g' => round($peak, 6),
            'mean_g' => round($mean, 6),
            'std_g' => round($std, 6),
            'fs_hz' => $fsHz !== null ? round($fsHz, 4) : null,
            'dominant_freq_hz' => $dominantFreqHz !== null ? round($dominantFreqHz, 4) : null,
        ];
    }
}
