<?php
// Generate synthetic sensor data for the next N days from baseline date.
// This script populates raw_samples, raw_batches, analysis_results, fft_results, temperature_readings.
//
// Usage:
// php scripts/generate_synthetic_week_from_baseline.php --machine_id=1 --base_date=2026-02-26 --days=7

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function out(string $text): void
{
    echo $text . PHP_EOL;
}

function opts(array $argv): array
{
    $options = [
        'machine_id' => 1,
        'base_date' => '2026-02-26',
        'days' => 7,
        'sample_rate_hz' => (int) env('SAMPLE_RATE_HZ', 200),
        'replace_existing_synth' => 1,
    ];

    foreach ($argv as $arg) {
        if (!str_starts_with($arg, '--')) {
            continue;
        }
        [$k, $v] = array_pad(explode('=', substr($arg, 2), 2), 2, null);
        if ($k !== null && array_key_exists($k, $options) && $v !== null && $v !== '') {
            $options[$k] = $v;
        }
    }

    $options['machine_id'] = (int) $options['machine_id'];
    $options['days'] = max(1, min(31, (int) $options['days']));
    $options['sample_rate_hz'] = max(1, (int) $options['sample_rate_hz']);
    $options['replace_existing_synth'] = (int) $options['replace_existing_synth'] === 1 ? 1 : 0;

    return $options;
}

function clamp(float $value, float $min, float $max): float
{
    return max($min, min($max, $value));
}

function seedNoise(string $key, float $amplitude): float
{
    $hash = crc32($key);
    $ratio = ($hash % 10000) / 10000.0; // 0..1
    return ($ratio * 2.0 - 1.0) * $amplitude; // -amp..+amp
}

function computeSpectrum(array $magnitudes, int $sampleRateHz): array
{
    $n = count($magnitudes);
    if ($n < 2) {
        return [null, [], []];
    }

    $mean = array_sum($magnitudes) / $n;
    $signal = [];
    foreach ($magnitudes as $v) {
        $signal[] = $v - $mean;
    }

    $half = (int) floor($n / 2);
    $freqs = [];
    $amps = [];
    $maxAmp = null;
    $maxFreq = null;

    for ($k = 0; $k <= $half; $k++) {
        $real = 0.0;
        $imag = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $angle = 2 * M_PI * $k * $i / $n;
            $real += $signal[$i] * cos($angle);
            $imag -= $signal[$i] * sin($angle);
        }

        $amp = sqrt(($real * $real) + ($imag * $imag)) / $n;
        $freq = ($k * $sampleRateHz) / $n;
        $freqs[] = round($freq, 4);
        $amps[] = round($amp, 6);

        if ($k > 0 && ($maxAmp === null || $amp > $maxAmp)) {
            $maxAmp = $amp;
            $maxFreq = $freq;
        }
    }

    return [$maxFreq !== null ? round($maxFreq, 4) : null, $freqs, $amps];
}

$o = opts($argv);
$machine = DB::table('machines')->where('id', $o['machine_id'])->first();
if (!$machine) {
    out('[ERROR] Machine not found: ' . $o['machine_id']);
    exit(1);
}

$baseStart = Carbon::createFromFormat('Y-m-d', (string) $o['base_date'])->startOfDay();
$baseEnd = $baseStart->copy()->endOfDay();
$warning = (float) ($machine->threshold_warning ?? 25.0);
$critical = (float) ($machine->threshold_critical ?? 28.0);

$baselineRows = DB::table('raw_samples')
    ->where('machine_id', $o['machine_id'])
    ->whereBetween('created_at', [$baseStart, $baseEnd])
    ->orderBy('created_at')
    ->orderBy('t_ms')
    ->orderBy('id')
    ->get(['created_at', 't_ms', 'ax_g', 'ay_g', 'az_g', 'temperature_c']);

if ($baselineRows->isEmpty()) {
    out('[ERROR] No baseline raw_samples found for ' . $baseStart->toDateString());
    exit(1);
}

$grouped = [];
foreach ($baselineRows as $row) {
    $key = Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
    if (!isset($grouped[$key])) {
        $grouped[$key] = [];
    }
    $grouped[$key][] = $row;
}

$synthStart = $baseStart->copy()->addDay();
$synthEnd = $baseEnd->copy()->addDays($o['days']);

if ($o['replace_existing_synth'] === 1) {
    $synthBatches = DB::table('raw_batches')
        ->where('machine_id', $o['machine_id'])
        ->whereBetween('captured_at', [$synthStart, $synthEnd])
        ->where('name', 'like', 'SYNTH_BASE_%')
        ->get(['id']);

    $batchIds = $synthBatches->pluck('id')->map(fn($v) => (string) $v)->toArray();
    if (!empty($batchIds)) {
        foreach (array_chunk($batchIds, 500) as $chunk) {
            DB::table('raw_samples')
                ->where('machine_id', $o['machine_id'])
                ->whereIn('batch_id', $chunk)
                ->delete();
        }
    }

    DB::table('analysis_results')
        ->where('machine_id', $o['machine_id'])
        ->whereBetween('created_at', [$synthStart, $synthEnd])
        ->where('name', 'like', 'SYNTH_BASE_%')
        ->delete();

    $analysisIds = DB::table('analysis_results')
        ->where('machine_id', $o['machine_id'])
        ->whereBetween('created_at', [$synthStart, $synthEnd])
        ->pluck('id')
        ->toArray();
    if (!empty($analysisIds)) {
        foreach (array_chunk($analysisIds, 500) as $chunk) {
            DB::table('fft_results')->whereIn('analysis_result_id', $chunk)->delete();
        }
    }

    DB::table('temperature_readings')
        ->where('machine_id', $o['machine_id'])
        ->whereBetween('recorded_at', [$synthStart, $synthEnd])
        ->delete();

    DB::table('raw_batches')
        ->where('machine_id', $o['machine_id'])
        ->whereBetween('captured_at', [$synthStart, $synthEnd])
        ->where('name', 'like', 'SYNTH_BASE_%')
        ->delete();
}

$rawInserted = 0;
$batchInserted = 0;
$analysisInserted = 0;
$tempInserted = 0;

DB::transaction(function () use (
    $o,
    $grouped,
    $baseStart,
    $warning,
    $critical,
    &$rawInserted,
    &$batchInserted,
    &$analysisInserted,
    &$tempInserted
): void {
    for ($dayOffset = 1; $dayOffset <= $o['days']; $dayOffset++) {
        $dayBiasAx = seedNoise("day{$dayOffset}:ax", 0.006);
        $dayBiasAy = seedNoise("day{$dayOffset}:ay", 0.006);
        $dayBiasAz = seedNoise("day{$dayOffset}:az", 0.008);
        $dayBiasTemp = seedNoise("day{$dayOffset}:temp", 0.35);

        foreach ($grouped as $baseTs => $samples) {
            $baseMoment = Carbon::parse($baseTs);
            $target = $baseMoment->copy()->addDays($dayOffset);

            $batchName = 'SYNTH_BASE_' . $baseStart->format('Ymd') . '_D' . $dayOffset . '_' . $target->format('His');
            $targetTs = $target->toDateTimeString();

            $batchId = DB::table('raw_batches')->insertGetId([
                'machine_id' => $o['machine_id'],
                'captured_at' => $targetTs,
                'batch_time' => $targetTs,
                'name' => $batchName,
                'created_at' => $targetTs,
                'updated_at' => $targetTs,
            ]);
            $batchInserted++;

            $rawChunk = [];
            $magnitudes = [];
            $sumTemp = 0.0;
            $countTemp = 0;

            foreach ($samples as $i => $s) {
                $axBase = (float) ($s->ax_g ?? 0.0);
                $ayBase = (float) ($s->ay_g ?? 0.0);
                $azBase = (float) ($s->az_g ?? 0.0);
                $tempBase = $s->temperature_c !== null ? (float) $s->temperature_c : null;
                $tms = $s->t_ms !== null ? (int) $s->t_ms : $i;

                // Small realistic fluctuation: +/- ~2.5% + tiny day drift.
                $ax = $axBase * (1.0 + seedNoise("{$dayOffset}:{$baseTs}:{$i}:ax", 0.025)) + $dayBiasAx;
                $ay = $ayBase * (1.0 + seedNoise("{$dayOffset}:{$baseTs}:{$i}:ay", 0.025)) + $dayBiasAy;
                $az = $azBase * (1.0 + seedNoise("{$dayOffset}:{$baseTs}:{$i}:az", 0.018)) + $dayBiasAz;

                $temp = null;
                if ($tempBase !== null) {
                    $temp = $tempBase + seedNoise("{$dayOffset}:{$baseTs}:{$i}:temp", 0.22) + $dayBiasTemp;
                    $temp = round(clamp($temp, $tempBase - 1.2, $tempBase + 1.2), 2);
                    $sumTemp += $temp;
                    $countTemp++;
                }

                $mag = sqrt(($ax * $ax) + ($ay * $ay) + ($az * $az));
                $magnitudes[] = $mag;

                $rawChunk[] = [
                    'name' => 'SYNTH_SAMPLE_' . $batchId,
                    'machine_id' => $o['machine_id'],
                    'batch_id' => (string) $batchId,
                    't_ms' => $tms,
                    'ax_g' => round($ax, 6),
                    'ay_g' => round($ay, 6),
                    'az_g' => round($az, 6),
                    'temperature_c' => $temp,
                    'created_at' => $targetTs,
                    'updated_at' => $targetTs,
                    'captured_at' => $targetTs,
                ];
            }

            foreach (array_chunk($rawChunk, 1000) as $chunk) {
                DB::table('raw_samples')->insert($chunk);
            }
            $rawInserted += count($rawChunk);

            if ($countTemp > 0) {
                $avgTemp = $sumTemp / $countTemp;
                DB::table('temperature_readings')->insert([
                    'machine_id' => $o['machine_id'],
                    'value' => $avgTemp,
                    'temperature_c' => $avgTemp,
                    'recorded_at' => $targetTs,
                    'created_at' => $targetTs,
                    'updated_at' => $targetTs,
                ]);
                $tempInserted++;
            }

            $n = count($magnitudes);
            $mean = array_sum($magnitudes) / $n;
            $sq = 0.0;
            $peak = 0.0;
            foreach ($magnitudes as $v) {
                $sq += ($v * $v);
                $abs = abs($v);
                if ($abs > $peak) {
                    $peak = $abs;
                }
            }
            $rms = sqrt($sq / $n);
            $var = 0.0;
            foreach ($magnitudes as $v) {
                $var += (($v - $mean) * ($v - $mean));
            }
            $std = sqrt($var / $n);

            [$dominantFreq, $freqs, $amps] = computeSpectrum($magnitudes, $o['sample_rate_hz']);

            $condition = 'NORMAL';
            if ($rms >= $critical) {
                $condition = 'CRITICAL';
            } elseif ($rms >= $warning) {
                $condition = 'WARNING';
            }

            $analysisId = DB::table('analysis_results')->insertGetId([
                'name' => 'SYNTH_BASE_' . $baseStart->format('Ymd') . '_D' . $dayOffset . '_' . $target->format('His'),
                'machine_id' => $o['machine_id'],
                'rms' => round($rms, 6),
                'rms_g' => round($rms, 6),
                'peak_amp' => round($peak, 6),
                'dominant_freq_hz' => $dominantFreq,
                'mean' => round($mean, 6),
                'std' => round($std, 6),
                'fs_hz' => $o['sample_rate_hz'],
                'n' => $n,
                'result' => 'synthetic_from_baseline',
                'condition_status' => $condition,
                'created_at' => $targetTs,
                'updated_at' => $targetTs,
            ]);
            $analysisInserted++;

            DB::table('fft_results')->insert([
                'analysis_result_id' => $analysisId,
                'frequencies' => json_encode($freqs),
                'amplitudes' => json_encode($amps),
                'created_at' => $targetTs,
                'updated_at' => $targetTs,
            ]);
        }
    }
});

$cacheKeys = [
    'dashboard_all_data',
    'dashboard_all_data_v2',
    'dashboard_total_samples',
    'api_dashboard_data',
    'api_machine_status',
    'api_top_machines_risk',
    'api_active_alerts',
    'fft_latest_' . $o['machine_id'],
];
foreach ($cacheKeys as $key) {
    Cache::forget($key);
}

out('[OK] Synthetic generation completed.');
out('[OK] raw_samples inserted: ' . $rawInserted);
out('[OK] raw_batches inserted: ' . $batchInserted);
out('[OK] analysis_results inserted: ' . $analysisInserted);
out('[OK] temperature_readings inserted: ' . $tempInserted);
out('[OK] target date range: ' . $synthStart->toDateString() . ' -> ' . $synthEnd->toDateString());
