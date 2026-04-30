<?php
// Import backup sensor history CSV into raw_samples + analysis_results + fft_results.
// Usage:
//   php scripts/import_backup_sensor_history.php --file="C:\path\sensor_history.csv" --machine_id=1
// Optional:
//   --replace_range=1 (default)  delete existing records in detected time range before import
//   --sample_rate_hz=200         used to synthesize t_ms and FFT frequency axis
//   --dry_run=1                  only scan file and print summary

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function cliOptions(array $argv): array
{
    $opts = [
        'file' => null,
        'machine_id' => 1,
        'replace_range' => 1,
        'sample_rate_hz' => (int) env('SAMPLE_RATE_HZ', 200),
        'dry_run' => 0,
    ];

    foreach ($argv as $arg) {
        if (!str_starts_with($arg, '--')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', substr($arg, 2), 2), 2, null);
        if ($key !== null && array_key_exists($key, $opts) && $value !== null && $value !== '') {
            $opts[$key] = $value;
        }
    }

    $opts['machine_id'] = (int) $opts['machine_id'];
    $opts['replace_range'] = (int) $opts['replace_range'] === 1 ? 1 : 0;
    $opts['sample_rate_hz'] = max(1, (int) $opts['sample_rate_hz']);
    $opts['dry_run'] = (int) $opts['dry_run'] === 1 ? 1 : 0;

    return $opts;
}

function out(string $text): void
{
    echo $text . PHP_EOL;
}

function openCsv(string $path): SplFileObject
{
    $csv = new SplFileObject($path, 'r');
    $csv->setFlags(
        SplFileObject::READ_CSV
        | SplFileObject::SKIP_EMPTY
        | SplFileObject::DROP_NEW_LINE
    );
    return $csv;
}

function normalizeHeader(string $header): string
{
    $header = trim($header);
    $header = preg_replace('/^\xEF\xBB\xBF/u', '', $header) ?? $header;
    $header = strtolower($header);
    return str_replace([' ', '-', '.'], '_', $header);
}

function parseTimestamp(string $value): ?Carbon
{
    $value = trim($value, " \t\n\r\0\x0B\"");
    if ($value === '') {
        return null;
    }
    try {
        return Carbon::parse($value);
    } catch (Throwable) {
        return null;
    }
}

function parseFloatCell($value): ?float
{
    if ($value === null) {
        return null;
    }
    $value = trim((string) $value, " \t\n\r\0\x0B\"");
    if ($value === '') {
        return null;
    }
    if (!is_numeric($value)) {
        return null;
    }
    return (float) $value;
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
    $frequencies = [];
    $amplitudes = [];

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

        $frequencies[] = round($freq, 4);
        $amplitudes[] = round($amp, 6);

        if ($k > 0 && ($maxAmp === null || $amp > $maxAmp)) {
            $maxAmp = $amp;
            $maxFreq = $freq;
        }
    }

    return [$maxFreq !== null ? round($maxFreq, 4) : null, $frequencies, $amplitudes];
}

$opts = cliOptions($argv);
if (!$opts['file']) {
    out('[ERROR] Missing --file argument.');
    exit(1);
}

$filePath = (string) $opts['file'];
if (!file_exists($filePath)) {
    out('[ERROR] File not found: ' . $filePath);
    exit(1);
}

$machine = DB::table('machines')->where('id', $opts['machine_id'])->first();
if (!$machine) {
    out('[ERROR] Machine not found for machine_id=' . $opts['machine_id']);
    exit(1);
}

$warningThreshold = (float) ($machine->threshold_warning ?? 25.0);
$criticalThreshold = (float) ($machine->threshold_critical ?? 28.0);

$scanCsv = openCsv($filePath);
$header = $scanCsv->fgetcsv();
if (!is_array($header) || empty($header)) {
    out('[ERROR] Invalid CSV header.');
    exit(1);
}

$indexMap = [];
foreach ($header as $idx => $cell) {
    $indexMap[normalizeHeader((string) $cell)] = $idx;
}

$requiredAliases = [
    'waktu' => ['waktu', 'timestamp', 'time'],
    'ax' => ['akselerasi_x_g', 'ax_g', 'x_g'],
    'ay' => ['akselerasi_y_g', 'ay_g', 'y_g'],
    'az' => ['akselerasi_z_g', 'az_g', 'z_g'],
    'temp' => ['suhu_c', 'temperature_c', 'temp_c'],
];

$indexes = [];
foreach ($requiredAliases as $key => $aliases) {
    $found = null;
    foreach ($aliases as $alias) {
        if (array_key_exists($alias, $indexMap)) {
            $found = $indexMap[$alias];
            break;
        }
    }
    if ($found === null) {
        out('[ERROR] Missing required column for ' . $key);
        exit(1);
    }
    $indexes[$key] = $found;
}

$totalRows = 0;
$minTs = null;
$maxTs = null;
$uniqueTs = [];

while (!$scanCsv->eof()) {
    $row = $scanCsv->fgetcsv();
    if (!is_array($row) || count($row) < 5) {
        continue;
    }
    $ts = parseTimestamp((string) ($row[$indexes['waktu']] ?? ''));
    if (!$ts) {
        continue;
    }
    $totalRows++;
    $tsKey = $ts->format('Y-m-d H:i:s');
    $uniqueTs[$tsKey] = true;

    if ($minTs === null || $ts->lt($minTs)) {
        $minTs = $ts->copy();
    }
    if ($maxTs === null || $ts->gt($maxTs)) {
        $maxTs = $ts->copy();
    }
}

if ($totalRows === 0 || $minTs === null || $maxTs === null) {
    out('[ERROR] No importable rows found.');
    exit(1);
}

out('[INFO] File: ' . $filePath);
out('[INFO] Machine: ' . ($machine->name ?? ('ID ' . $opts['machine_id'])) . ' (ID ' . $opts['machine_id'] . ')');
out('[INFO] Rows: ' . $totalRows);
out('[INFO] Unique timestamps: ' . count($uniqueTs));
out('[INFO] Range: ' . $minTs->toDateTimeString() . ' -> ' . $maxTs->toDateTimeString());
out('[INFO] Sample rate (Hz): ' . $opts['sample_rate_hz']);

if ($opts['dry_run'] === 1) {
    out('[OK] Dry run complete.');
    exit(0);
}

$start = $minTs->toDateTimeString();
$end = $maxTs->toDateTimeString();

if ($opts['replace_range'] === 1) {
    $analysisIds = DB::table('analysis_results')
        ->where('machine_id', $opts['machine_id'])
        ->whereBetween('created_at', [$start, $end])
        ->pluck('id')
        ->toArray();

    foreach (array_chunk($analysisIds, 500) as $chunk) {
        DB::table('fft_results')->whereIn('analysis_result_id', $chunk)->delete();
    }

    DB::table('analysis_results')
        ->where('machine_id', $opts['machine_id'])
        ->whereBetween('created_at', [$start, $end])
        ->delete();

    DB::table('raw_samples')
        ->where('machine_id', $opts['machine_id'])
        ->whereBetween('created_at', [$start, $end])
        ->delete();

    DB::table('raw_batches')
        ->where('machine_id', $opts['machine_id'])
        ->whereBetween('captured_at', [$start, $end])
        ->delete();

    DB::table('temperature_readings')
        ->where('machine_id', $opts['machine_id'])
        ->whereBetween('recorded_at', [$start, $end])
        ->delete();

    out('[INFO] Existing data in range deleted.');
}

$rawSampleColumns = array_flip(DB::getSchemaBuilder()->getColumnListing('raw_samples'));
$rawBatchColumns = array_flip(DB::getSchemaBuilder()->getColumnListing('raw_batches'));

$importCsv = openCsv($filePath);
$importCsv->fgetcsv(); // skip header

$currentTsKey = null;
$groupRows = [];
$importedRaw = 0;
$importedBatch = 0;
$importedAnalysis = 0;

$flush = function (?string $tsKey, array $rows) use (
    &$importedRaw,
    &$importedBatch,
    &$importedAnalysis,
    $opts,
    $warningThreshold,
    $criticalThreshold,
    $rawSampleColumns,
    $rawBatchColumns
): void {
    if ($tsKey === null || empty($rows)) {
        return;
    }

    $capturedAt = Carbon::parse($tsKey);
    $nowTs = $capturedAt->toDateTimeString();
    $sampleRate = (int) $opts['sample_rate_hz'];
    $dtMs = (int) max(1, round(1000 / $sampleRate));

    $batchData = [
        'machine_id' => $opts['machine_id'],
        'captured_at' => $nowTs,
        'batch_time' => $nowTs,
        'name' => 'IMPORT_BACKUP_' . $capturedAt->format('Ymd_His'),
        'created_at' => $nowTs,
        'updated_at' => $nowTs,
    ];
    $batchData = array_intersect_key($batchData, $rawBatchColumns);
    $batchId = DB::table('raw_batches')->insertGetId($batchData);
    $importedBatch++;

    $rawInsert = [];
    $magnitudes = [];
    $axSum = 0.0;
    $aySum = 0.0;
    $azSum = 0.0;
    $tempSum = 0.0;
    $tempCount = 0;

    foreach ($rows as $i => $r) {
        $ax = (float) $r['ax'];
        $ay = (float) $r['ay'];
        $az = (float) $r['az'];
        $temp = $r['temp'];
        $mag = sqrt(($ax * $ax) + ($ay * $ay) + ($az * $az));

        $magnitudes[] = $mag;
        $axSum += $ax;
        $aySum += $ay;
        $azSum += $az;
        if ($temp !== null) {
            $tempSum += (float) $temp;
            $tempCount++;
        }

        $row = [
            'name' => 'IMPORT_SAMPLE_' . $batchId,
            'machine_id' => $opts['machine_id'],
            'batch_id' => (string) $batchId,
            't_ms' => $i * $dtMs,
            'ax_g' => $ax,
            'ay_g' => $ay,
            'az_g' => $az,
            'temperature_c' => $temp,
            'captured_at' => $nowTs,
            'created_at' => $nowTs,
            'updated_at' => $nowTs,
        ];
        $rawInsert[] = array_intersect_key($row, $rawSampleColumns);
    }

    foreach (array_chunk($rawInsert, 1000) as $chunk) {
        DB::table('raw_samples')->insert($chunk);
    }
    $importedRaw += count($rawInsert);

    if ($tempCount > 0) {
        $avgTemp = $tempSum / $tempCount;
        DB::table('temperature_readings')->insert([
            'machine_id' => $opts['machine_id'],
            'value' => $avgTemp,
            'temperature_c' => $avgTemp,
            'recorded_at' => $nowTs,
            'created_at' => $nowTs,
            'updated_at' => $nowTs,
        ]);
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
    $variance = 0.0;
    foreach ($magnitudes as $v) {
        $variance += (($v - $mean) * ($v - $mean));
    }
    $std = sqrt($variance / $n);

    [$dominantFreq, $freqs, $amps] = computeSpectrum($magnitudes, $sampleRate);

    $status = 'NORMAL';
    if ($rms >= $criticalThreshold) {
        $status = 'CRITICAL';
    } elseif ($rms >= $warningThreshold) {
        $status = 'WARNING';
    }

    $analysisId = DB::table('analysis_results')->insertGetId([
        'name' => 'Imported Analysis ' . $capturedAt->format('Ymd_His'),
        'machine_id' => $opts['machine_id'],
        'rms' => round($rms, 6),
        'rms_g' => round($rms, 6),
        'peak_amp' => round($peak, 6),
        'dominant_freq_hz' => $dominantFreq,
        'mean' => round($mean, 6),
        'std' => round($std, 6),
        'fs_hz' => $sampleRate,
        'n' => $n,
        'result' => 'import_backup_csv',
        'condition_status' => $status,
        'created_at' => $nowTs,
        'updated_at' => $nowTs,
    ]);

    DB::table('fft_results')->insert([
        'analysis_result_id' => $analysisId,
        'frequencies' => json_encode($freqs),
        'amplitudes' => json_encode($amps),
        'created_at' => $nowTs,
        'updated_at' => $nowTs,
    ]);

    $importedAnalysis++;
};

while (!$importCsv->eof()) {
    $row = $importCsv->fgetcsv();
    if (!is_array($row) || count($row) < 5) {
        continue;
    }

    $ts = parseTimestamp((string) ($row[$indexes['waktu']] ?? ''));
    $ax = parseFloatCell($row[$indexes['ax']] ?? null);
    $ay = parseFloatCell($row[$indexes['ay']] ?? null);
    $az = parseFloatCell($row[$indexes['az']] ?? null);
    $temp = parseFloatCell($row[$indexes['temp']] ?? null);

    if (!$ts || $ax === null || $ay === null || $az === null) {
        continue;
    }

    $tsKey = $ts->format('Y-m-d H:i:s');
    if ($currentTsKey === null) {
        $currentTsKey = $tsKey;
    }

    if ($tsKey !== $currentTsKey) {
        $flush($currentTsKey, $groupRows);
        $groupRows = [];
        $currentTsKey = $tsKey;
    }

    $groupRows[] = [
        'ax' => $ax,
        'ay' => $ay,
        'az' => $az,
        'temp' => $temp,
    ];
}

$flush($currentTsKey, $groupRows);

out('[OK] Import completed.');
out('[OK] Raw samples inserted: ' . $importedRaw);
out('[OK] Raw batches inserted: ' . $importedBatch);
out('[OK] Analysis results inserted: ' . $importedAnalysis);
