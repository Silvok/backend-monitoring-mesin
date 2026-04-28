<?php
// Seed dummy raw_samples within the last N hours for demo/checklist validation.
// Usage:
//   php scripts/seed_dummy_recent_raw_samples.php
//   php scripts/seed_dummy_recent_raw_samples.php --machine_id=1 --samples=120 --hours=24

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function parseCliOptions(array $argv): array
{
    $options = [
        'machine_id' => null,
        'samples' => 60,
        'hours' => 24,
    ];

    foreach ($argv as $arg) {
        if (!str_starts_with($arg, '--')) {
            continue;
        }

        $parts = explode('=', substr($arg, 2), 2);
        $key = $parts[0] ?? '';
        $value = $parts[1] ?? null;

        if ($value === null || $value === '') {
            continue;
        }

        if (array_key_exists($key, $options)) {
            $options[$key] = $value;
        }
    }

    $options['machine_id'] = $options['machine_id'] !== null ? (int) $options['machine_id'] : null;
    $options['samples'] = max(1, (int) $options['samples']);
    $options['hours'] = max(1, (int) $options['hours']);

    return $options;
}

function printLine(string $message): void
{
    echo $message . PHP_EOL;
}

$options = parseCliOptions($argv);

$machineQuery = DB::table('machines')->select('id', 'name');
if ($options['machine_id'] !== null) {
    $machineQuery->where('id', $options['machine_id']);
}
$machine = $machineQuery->orderBy('id')->first();

if (!$machine) {
    printLine('[ERROR] Machine not found. Use --machine_id=<id> with a valid machine.');
    exit(1);
}

$now = now();
$start = $now->copy()->subHours($options['hours']);
$samples = $options['samples'];
$batchToken = 'demo-' . $now->format('YmdHis') . '-' . Str::lower(Str::random(5));
$batchName = 'demo_checklist_' . $batchToken;

$intervalSeconds = $samples > 1
    ? (int) floor(($options['hours'] * 3600) / ($samples - 1))
    : 0;

$rows = [];
for ($i = 0; $i < $samples; $i++) {
    $sampleTime = $start->copy()->addSeconds($intervalSeconds * $i);

    // Generate deterministic-but-varied vibration values for demo.
    $phase = $i % 30;
    $az = 18.0 + ($phase * 0.45); // spans normal-warning-danger ranges
    $ax = 8.0 + ($phase * 0.12);
    $ay = 9.0 + ($phase * 0.10);

    $rows[] = [
        'name' => $machine->name . '_dummy_' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
        'machine_id' => $machine->id,
        'batch_id' => $batchToken,
        't_ms' => $i * 1000,
        'ax_g' => round($ax, 4),
        'ay_g' => round($ay, 4),
        'az_g' => round($az, 4),
        'temperature_c' => round(48.0 + (($i % 20) * 0.7), 2),
        'captured_at' => $sampleTime,
        'created_at' => $sampleTime,
        'updated_at' => $sampleTime,
    ];
}

DB::transaction(function () use ($machine, $batchName, $start, $now, $rows): void {
    DB::table('raw_batches')->insert([
        'machine_id' => $machine->id,
        'name' => $batchName,
        'captured_at' => $start,
        'batch_time' => $start,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    foreach (array_chunk($rows, 500) as $chunk) {
        DB::table('raw_samples')->insert($chunk);
    }
});

$count24h = (int) DB::table('raw_samples')
    ->where('machine_id', $machine->id)
    ->where('created_at', '>=', now()->subDay())
    ->count();

printLine('[OK] Dummy samples inserted successfully.');
printLine('Machine      : ' . $machine->name . ' (ID ' . $machine->id . ')');
printLine('Batch ID     : ' . $batchToken);
printLine('Samples      : ' . $samples);
printLine('Time Range   : ' . $start->toDateTimeString() . ' -> ' . $now->toDateTimeString());
printLine('Last 24h Row : ' . $count24h);
