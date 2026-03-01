<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Machine;
use App\Models\User;
use App\Events\AnalysisUpdated;

class AnalyzeBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function handle(): void
    {
        try {
            if (!Schema::hasTable('raw_samples') || !Schema::hasTable('analysis_results')) {
                \Log::warning('AnalyzeBatchJob skipped: missing tables', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $rawSampleColumns = array_flip(Schema::getColumnListing('raw_samples'));
            $analysisColumns = array_flip(Schema::getColumnListing('analysis_results'));

            $query = DB::table('raw_samples');
            if (array_key_exists('raw_batch_id', $rawSampleColumns)) {
                $query->where('raw_batch_id', $this->batchId);
            } elseif (array_key_exists('batch_id', $rawSampleColumns)) {
                $query->where('batch_id', $this->batchId);
            } else {
                \Log::warning('AnalyzeBatchJob skipped: no batch id column', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $selectCols = [];
            foreach (['t_ms', 'ax_g', 'ay_g', 'az_g', 'value', 'machine_id'] as $col) {
                if (array_key_exists($col, $rawSampleColumns)) {
                    $selectCols[] = $col;
                }
            }
            if (empty($selectCols)) {
                \Log::warning('AnalyzeBatchJob skipped: no sample columns', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            if (array_key_exists('t_ms', $rawSampleColumns)) {
                $query->orderBy('t_ms');
            }
            $samples = $query->select($selectCols)->get();
            if ($samples->isEmpty()) {
                \Log::warning('AnalyzeBatchJob skipped: empty samples', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $values = [];
            $axValues = [];
            $ayValues = [];
            $azValues = [];
            $timeValues = [];
            $hasAxis = array_key_exists('ax_g', $rawSampleColumns)
                || array_key_exists('ay_g', $rawSampleColumns)
                || array_key_exists('az_g', $rawSampleColumns);

            foreach ($samples as $row) {
                $timeValues[] = $row->t_ms ?? null;
                if ($hasAxis) {
                    $ax = isset($row->ax_g) ? (float) $row->ax_g : 0.0;
                    $ay = isset($row->ay_g) ? (float) $row->ay_g : 0.0;
                    $az = isset($row->az_g) ? (float) $row->az_g : 0.0;
                    $values[] = sqrt(($ax * $ax) + ($ay * $ay) + ($az * $az));
                    $axValues[] = $ax;
                    $ayValues[] = $ay;
                    $azValues[] = $az;
                } elseif (isset($row->value)) {
                    $value = (float) $row->value;
                    $values[] = $value;
                    $axValues[] = $value;
                    $ayValues[] = 0.0;
                    $azValues[] = 0.0;
                }
            }

            if (empty($values)) {
                \Log::warning('AnalyzeBatchJob skipped: no numeric values', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $count = count($values);
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
            $rmsG = sqrt($squareSum / $count);
            $variance = 0.0;
            foreach ($values as $v) {
                $variance += pow($v - $mean, 2);
            }
            $std = sqrt($variance / $count);

            $velocityRmsMmS = null;
            $velocityPeakMmS = null;
            $velocityMeanMmS = null;
            $velocityStdMmS = null;
            $fsHz = null;
            $dominantFreqHz = null;
            $fftFrequencies = [];
            $fftAmplitudes = [];
            $defaultFsHz = (float) env('SAMPLE_RATE_HZ', 1000);
            $defaultDt = $defaultFsHz > 0 ? (1 / $defaultFsHz) : null;
            $dtSamples = [];
            for ($i = 1; $i < $count; $i++) {
                if ($timeValues[$i] !== null && $timeValues[$i - 1] !== null) {
                    $dtMs = $timeValues[$i] - $timeValues[$i - 1];
                    if ($dtMs > 0) {
                        $dtSamples[$i] = $dtMs / 1000;
                    }
                }
            }
            $avgDt = !empty($dtSamples) ? (array_sum($dtSamples) / count($dtSamples)) : $defaultDt;
            if ($avgDt !== null && $avgDt > 0) {
                $fsHz = 1 / $avgDt;
                $axMean = array_sum($axValues) / $count;
                $ayMean = array_sum($ayValues) / $count;
                $azMean = array_sum($azValues) / $count;
                $gToMs2 = 9.80665;

                // Band-pass 10–500 Hz (ISO-style). Limited by Nyquist.
                $bandLowHz = 10.0;
                $bandHighHz = min(500.0, $fsHz / 2);

                // Prepare acceleration signals (m/s^2), mean-removed.
                $axSignal = [];
                $aySignal = [];
                $azSignal = [];
                for ($i = 0; $i < $count; $i++) {
                    $axSignal[] = ($axValues[$i] - $axMean) * $gToMs2;
                    $aySignal[] = ($ayValues[$i] - $ayMean) * $gToMs2;
                    $azSignal[] = ($azValues[$i] - $azMean) * $gToMs2;
                }

                // Simple DFT/IDFT (N is small: ~256). Uses full spectrum so negative freqs handled.
                $dft = function (array $signal) use ($count) {
                    $real = array_fill(0, $count, 0.0);
                    $imag = array_fill(0, $count, 0.0);
                    for ($k = 0; $k < $count; $k++) {
                        $sumReal = 0.0;
                        $sumImag = 0.0;
                        for ($n = 0; $n < $count; $n++) {
                            $angle = -2 * M_PI * $k * $n / $count;
                            $cos = cos($angle);
                            $sin = sin($angle);
                            $sumReal += $signal[$n] * $cos;
                            $sumImag += $signal[$n] * $sin;
                        }
                        $real[$k] = $sumReal;
                        $imag[$k] = $sumImag;
                    }
                    return [$real, $imag];
                };
                $idft = function (array $real, array $imag) use ($count) {
                    $signal = array_fill(0, $count, 0.0);
                    for ($n = 0; $n < $count; $n++) {
                        $sum = 0.0;
                        for ($k = 0; $k < $count; $k++) {
                            $angle = 2 * M_PI * $k * $n / $count;
                            $sum += $real[$k] * cos($angle) - $imag[$k] * sin($angle);
                        }
                        $signal[$n] = $sum / $count;
                    }
                    return $signal;
                };

                $toVelocity = function (array $signal) use ($dft, $idft, $count, $fsHz, $bandLowHz, $bandHighHz) {
                    [$real, $imag] = $dft($signal);
                    $vReal = array_fill(0, $count, 0.0);
                    $vImag = array_fill(0, $count, 0.0);
                    for ($k = 0; $k < $count; $k++) {
                        // Map index to frequency (Hz), including negative frequencies.
                        $freq = ($k <= $count / 2)
                            ? ($k * $fsHz / $count)
                            : (($k - $count) * $fsHz / $count);
                        $absFreq = abs($freq);
                        if ($absFreq < $bandLowHz || $absFreq > $bandHighHz || $freq == 0.0) {
                            continue;
                        }
                        $omega = 2 * M_PI * $freq;
                        // V = A / (j*omega) => real = imag/omega, imag = -real/omega
                        $vReal[$k] = $imag[$k] / $omega;
                        $vImag[$k] = -$real[$k] / $omega;
                    }
                    return $idft($vReal, $vImag);
                };

                $vx = $toVelocity($axSignal);
                $vy = $toVelocity($aySignal);
                $vz = $toVelocity($azSignal);

                $velocityMagnitudes = [];
                for ($i = 0; $i < $count; $i++) {
                    $velocityMagnitudes[] = sqrt(($vx[$i] * $vx[$i]) + ($vy[$i] * $vy[$i]) + ($vz[$i] * $vz[$i]));
                }

                $velocityCount = count($velocityMagnitudes);
                $velocitySum = array_sum($velocityMagnitudes);
                $velocityMean = $velocitySum / $velocityCount;
                $velocitySquareSum = 0.0;
                $velocityPeak = 0.0;
                foreach ($velocityMagnitudes as $velocity) {
                    $velocitySquareSum += ($velocity * $velocity);
                    if ($velocity > $velocityPeak) {
                        $velocityPeak = $velocity;
                    }
                }
                $velocityRms = sqrt($velocitySquareSum / $velocityCount);
                $velocityVariance = 0.0;
                foreach ($velocityMagnitudes as $velocity) {
                    $velocityVariance += pow($velocity - $velocityMean, 2);
                }
                $velocityStd = sqrt($velocityVariance / $velocityCount);

                $velocityRmsMmS = $velocityRms * 1000;
                $velocityPeakMmS = $velocityPeak * 1000;
                $velocityMeanMmS = $velocityMean * 1000;
                $velocityStdMmS = $velocityStd * 1000;

                // Simple DFT for dominant frequency (acceleration domain, in g)
                $signal = array_map(function ($val) use ($mean) {
                    return $val - $mean;
                }, $values);
                $half = (int) floor($count / 2);
                for ($k = 0; $k <= $half; $k++) {
                    $real = 0.0;
                    $imag = 0.0;
                    for ($nIdx = 0; $nIdx < $count; $nIdx++) {
                        $angle = 2 * M_PI * $k * $nIdx / $count;
                        $real += $signal[$nIdx] * cos($angle);
                        $imag -= $signal[$nIdx] * sin($angle);
                    }
                    $amp = sqrt(($real * $real) + ($imag * $imag)) / $count;
                    $freq = ($k * $fsHz) / $count;
                    $fftFrequencies[] = round($freq, 4);
                    $fftAmplitudes[] = round($amp, 6);
                }
                if (count($fftAmplitudes) > 1) {
                    $maxIdx = 1;
                    $maxAmp = $fftAmplitudes[1];
                    for ($i = 2; $i < count($fftAmplitudes); $i++) {
                        if ($fftAmplitudes[$i] > $maxAmp) {
                            $maxAmp = $fftAmplitudes[$i];
                            $maxIdx = $i;
                        }
                    }
                    $dominantFreqHz = $fftFrequencies[$maxIdx] ?? null;
                }
            } else {
                \Log::warning('AnalyzeBatchJob: missing sample timing, velocity RMS fallback to acceleration', [
                    'batch_id' => $this->batchId ?? null,
                ]);
            }

            $machineId = $samples->first()->machine_id ?? null;
            $warningThreshold = 21.84;
            $criticalThreshold = 25.11;
            $machine = null;
            if ($machineId) {
                $machine = Machine::find($machineId);
                if ($machine) {
                    $warningThreshold = (float) ($machine->threshold_warning ?? $warningThreshold);
                    $criticalThreshold = (float) ($machine->threshold_critical ?? $criticalThreshold);
                }
            }

            $conditionStatus = 'NORMAL';
            $rmsForStatus = $velocityRmsMmS ?? $rmsG;
            if ($rmsForStatus >= $criticalThreshold) {
                $conditionStatus = 'CRITICAL';
            } elseif ($rmsForStatus >= $warningThreshold) {
                $conditionStatus = 'WARNING';
            }

            $analysisData = [
                'machine_id' => $machineId,
                'batch_id' => $this->batchId,
                'raw_batch_id' => $this->batchId,
                'rms' => $velocityRmsMmS ?? $rmsG,
                'rms_g' => $rmsG,
                'peak_amp' => $velocityPeakMmS ?? $peak,
                'mean' => $velocityMeanMmS ?? $mean,
                'std' => $velocityStdMmS ?? $std,
                'fs_hz' => $fsHz,
                'n' => $count,
                'dominant_freq_hz' => $dominantFreqHz,
                'status' => 'done',
                'condition_status' => $conditionStatus,
                'name' => 'Analysis ' . now()->format('Ymd_His'),
                'result' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $analysisData = array_intersect_key($analysisData, $analysisColumns);

            if (!empty($analysisData)) {
                $analysisId = DB::table('analysis_results')->insertGetId($analysisData);
                if ($analysisId && Schema::hasTable('fft_results') && !empty($fftFrequencies) && !empty($fftAmplitudes)) {
                    DB::table('fft_results')->insert([
                        'analysis_result_id' => $analysisId,
                        'frequencies' => json_encode($fftFrequencies),
                        'amplitudes' => json_encode($fftAmplitudes),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if ($analysisId && Schema::hasTable('notifications') && in_array($conditionStatus, ['WARNING', 'CRITICAL'], true)) {
                    $machineName = $machine->name ?? 'Mesin';
                    $title = "Alert {$conditionStatus}: {$machineName}";
                    $rmsLabel = $rmsForStatus !== null ? number_format($rmsForStatus, 3) . ' mm/s' : '-';
                    $message = "RMS {$rmsLabel} melebihi ambang {$conditionStatus}.";
                    $payload = [
                        'analysis_id' => $analysisId,
                        'rms' => $rmsForStatus,
                        'status' => $conditionStatus,
                    ];
                    $now = now();
                    $users = User::query()->select('id')->get();
                    foreach ($users as $user) {
                        DB::table('notifications')->insert([
                            'user_id' => $user->id,
                            'machine_id' => $machineId,
                            'type' => 'ALERT',
                            'severity' => $conditionStatus,
                            'title' => $title,
                            'message' => $message,
                            'payload' => json_encode($payload),
                            'is_read' => false,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }

                if ($analysisId && $machine) {
                    broadcast(new AnalysisUpdated(
                        (int) $machine->id,
                        (string) $machine->name,
                        $machine->location,
                        (string) $conditionStatus,
                        $analysisData['rms'] ?? null,
                        $analysisData['peak_amp'] ?? null,
                        $analysisData['dominant_freq_hz'] ?? null,
                        now()->format('l, d-m-Y H:i')
                    ));
                }
            }

            // Invalidate dashboard caches to show latest RMS/status
            Cache::forget('dashboard_all_data');
            Cache::forget('api_dashboard_data');
            Cache::forget('api_machine_status');
            Cache::forget('api_top_machines_risk');
        } catch (\Throwable $e) {
            \Log::error('AnalyzeBatchJob FAILED', [
                'batch_id' => $this->batchId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    // ...existing code...
}
