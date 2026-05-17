<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$options = getopt('', [
    'file:',
    'machine::',
    'batch-size::',
    'date::',
    'delete-existing::',
    'skip-zero::',
]);

$file = $options['file'] ?? null;
if (!$file) {
    fwrite(STDERR, "Usage: php scripts/import_sensor_history_csv.php --file=<path> [--machine=1] [--batch-size=1000] [--date=YYYY-MM-DD] [--delete-existing=1] [--skip-zero=1]\n");
    exit(1);
}

$machineId = isset($options['machine']) ? (int) $options['machine'] : 1;
$batchSize = isset($options['batch-size']) ? max(100, (int) $options['batch-size']) : 1000;
$dateFilter = $options['date'] ?? null;
$deleteExisting = isset($options['delete-existing']) ? ((int) $options['delete-existing'] === 1) : true;
$skipZero = isset($options['skip-zero']) ? ((int) $options['skip-zero'] === 1) : true;

if (!is_file($file)) {
    fwrite(STDERR, "File not found: {$file}\n");
    exit(1);
}

if ($dateFilter !== null) {
    $validDate = DateTime::createFromFormat('Y-m-d', $dateFilter);
    if (!$validDate || $validDate->format('Y-m-d') !== $dateFilter) {
        fwrite(STDERR, "Invalid --date format. Use YYYY-MM-DD.\n");
        exit(1);
    }
}

$db = $app->make('db');

$machineExists = $db->table('machines')->where('id', $machineId)->exists();
if (!$machineExists) {
    fwrite(STDERR, "Machine id {$machineId} not found in machines table.\n");
    exit(1);
}

if ($deleteExisting && $dateFilter !== null) {
    $start = Carbon::parse($dateFilter)->startOfDay()->format('Y-m-d H:i:s');
    $end = Carbon::parse($dateFilter)->addDay()->startOfDay()->format('Y-m-d H:i:s');

    $deleted = $db->table('raw_samples')
        ->where('machine_id', $machineId)
        ->where('created_at', '>=', $start)
        ->where('created_at', '<', $end)
        ->delete();

    echo "Deleted existing rows for machine {$machineId} on {$dateFilter}: {$deleted}\n";
}

$handle = fopen($file, 'rb');
if ($handle === false) {
    fwrite(STDERR, "Unable to open file: {$file}\n");
    exit(1);
}

$header = fgetcsv($handle);
if ($header === false) {
    fclose($handle);
    fwrite(STDERR, "CSV appears empty.\n");
    exit(1);
}

$rows = [];
$inserted = 0;
$skipped = 0;
$skippedZero = 0;

$flush = static function (array &$buffer, $dbTable) use (&$inserted): void {
    if (empty($buffer)) {
        return;
    }
    $dbTable->insert($buffer);
    $inserted += count($buffer);
    $buffer = [];
};

$rawSamples = $db->table('raw_samples');

while (($data = fgetcsv($handle)) !== false) {
    if (count($data) < 6) {
        $skipped++;
        continue;
    }

    $waktu = trim((string) $data[0]);
    $ax = is_numeric($data[1]) ? (float) $data[1] : null;
    $ay = is_numeric($data[2]) ? (float) $data[2] : null;
    $az = is_numeric($data[3]) ? (float) $data[3] : null;
    $temp = is_numeric($data[4]) ? (float) $data[4] : null;

    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $waktu);
    if (!$dt) {
        $skipped++;
        continue;
    }

    $createdAt = $dt->format('Y-m-d H:i:s');
    $rowDate = $dt->format('Y-m-d');

    if ($dateFilter !== null && $rowDate !== $dateFilter) {
        $skipped++;
        continue;
    }

    if (
        $skipZero
        && $ax !== null
        && $ay !== null
        && $az !== null
        && $ax == 0.0
        && $ay == 0.0
        && $az == 0.0
    ) {
        $skippedZero++;
        continue;
    }

    $rows[] = [
        'name' => 'import_csv_sensor_history',
        'machine_id' => $machineId,
        'ax_g' => $ax,
        'ay_g' => $ay,
        'az_g' => $az,
        'temperature_c' => $temp,
        'captured_at' => $createdAt,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ];

    if (count($rows) >= $batchSize) {
        $flush($rows, $rawSamples);
    }
}

$flush($rows, $rawSamples);
fclose($handle);

echo "Import complete.\n";
echo "Inserted: {$inserted}\n";
echo "Skipped: {$skipped}\n";
echo "Skipped zero XYZ rows: {$skippedZero}\n";
