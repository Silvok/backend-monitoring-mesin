<?php
// Simple validation checklist for monitoring system
// Run: php scripts/validate_system.php

function envValue($key, $default = null) {
    $path = __DIR__ . '/../.env';
    if (!file_exists($path)) return $default;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        if (trim($parts[0]) === $key) return trim($parts[1]);
    }
    return $default;
}

function check($label, $ok, $details = '') {
    $status = $ok ? 'PASS' : 'FAIL';
    echo "[$status] $label";
    if ($details !== '') echo " => $details";
    echo PHP_EOL;
}

// Target thresholds (adjust if needed)
$targetWarning = 21.84;
$targetCritical = 25.11;

// DB config
$dbHost = envValue('DB_HOST', '127.0.0.1');
$dbPort = envValue('DB_PORT', '3306');
$dbName = envValue('DB_DATABASE', '');
$dbUser = envValue('DB_USERNAME', '');
$dbPass = envValue('DB_PASSWORD', '');

$pdo = null;
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    check('DB connection', true, $dbName);
} catch (Throwable $e) {
    check('DB connection', false, $e->getMessage());
    exit(1);
}

// Check machines thresholds
$machines = $pdo->query("SELECT id, name, threshold_warning, threshold_critical FROM machines")->fetchAll(PDO::FETCH_ASSOC);
if (!$machines) {
    check('Machines exist', false, 'No machines in DB');
} else {
    check('Machines exist', true, count($machines) . ' machine(s)');
    $mismatch = [];
    foreach ($machines as $m) {
        $w = (float) $m['threshold_warning'];
        $c = (float) $m['threshold_critical'];
        if (abs($w - $targetWarning) > 0.01 || abs($c - $targetCritical) > 0.01) {
            $mismatch[] = "{$m['name']} (id {$m['id']}): {$w}/{$c}";
        }
    }
    check('Thresholds match target', count($mismatch) === 0, $mismatch ? implode('; ', $mismatch) : "{$targetWarning}/{$targetCritical}");
}

// Check analysis results presence
$countAnalysis = (int) $pdo->query("SELECT COUNT(*) FROM analysis_results")->fetchColumn();
check('Analysis results available', $countAnalysis > 0, $countAnalysis . ' rows');

// Check raw samples recent (last 24h)
$countRaw = (int) $pdo->query("SELECT COUNT(*) FROM raw_samples WHERE created_at >= NOW() - INTERVAL 1 DAY")->fetchColumn();
check('Raw samples in last 24h', $countRaw > 0, $countRaw . ' rows');

// Check cache sampling interval (if cache table exists)
$hasCache = $pdo->query("SHOW TABLES LIKE 'cache'")->fetchColumn();
if ($hasCache) {
    $cacheKey = $pdo->prepare("SELECT `key`, `value` FROM cache WHERE `key` LIKE ?");
    $cacheKey->execute(['%sampling_interval_minutes%']);
    $row = $cacheKey->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        check('Sampling interval (cache)', true, $row['key']);
    } else {
        check('Sampling interval (cache)', false, 'Not set (default 1 minute)');
    }
} else {
    check('Cache table exists', false, 'No cache table');
}

// Scan code for old threshold literals (2.8 / 7.1)
$scanDirs = [__DIR__ . '/../resources', __DIR__ . '/../app'];
$badHits = [];
foreach ($scanDirs as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file->isFile()) continue;
        $path = $file->getPathname();
        if (str_contains($path, 'welcome.blade.php')) continue;
        $content = @file_get_contents($path);
        if ($content === false) continue;
        if (preg_match('/\\b2\\.8\\b|\\b7\\.1\\b/', $content)) {
            $badHits[] = $path;
        }
    }
}
check('No old thresholds in code', count($badHits) === 0, $badHits ? implode(', ', array_slice($badHits, 0, 5)) : 'clean');
if (count($badHits) > 5) {
    echo "…and " . (count($badHits) - 5) . " more files" . PHP_EOL;
}

echo PHP_EOL . "Checklist finished." . PHP_EOL;
