<?php
// Deployment readiness checklist
// Run: php scripts/deploy_checklist.php

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

function check($label, $ok, $details = '', $level = 'PASS') {
    $status = $ok ? $level : ($level === 'WARN' ? 'WARN' : 'FAIL');
    echo "[$status] $label";
    if ($details !== '') echo " => $details";
    echo PHP_EOL;
}

$root = realpath(__DIR__ . '/..');

// Files & config
check('.env exists', file_exists($root . '/.env'));
$appKey = envValue('APP_KEY', '');
check('APP_KEY set', !empty($appKey), $appKey ? 'set' : 'missing');
$appEnv = envValue('APP_ENV', 'local');
check('APP_ENV', true, $appEnv, 'PASS');
$appDebug = envValue('APP_DEBUG', 'true');
check('APP_DEBUG disabled', $appDebug === 'false', 'current=' . $appDebug, 'WARN');

check('storage/ writable', is_writable($root . '/storage'), '', is_writable($root . '/storage') ? 'PASS' : 'FAIL');
check('bootstrap/cache writable', is_writable($root . '/bootstrap/cache'), '', is_writable($root . '/bootstrap/cache') ? 'PASS' : 'FAIL');

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

// Core tables
$tables = ['machines', 'analysis_results', 'raw_samples', 'roles', 'users'];
foreach ($tables as $table) {
    $exists = (bool) $pdo->query("SHOW TABLES LIKE '{$table}'")->fetchColumn();
    check("Table exists: {$table}", $exists);
}

// Basic data presence
$machineCount = (int) $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();
check('Machines exist', $machineCount > 0, $machineCount . ' machine(s)');

$analysisCount = (int) $pdo->query("SELECT COUNT(*) FROM analysis_results")->fetchColumn();
check('Analysis results available', $analysisCount > 0, $analysisCount . ' rows');

$rawCount = (int) $pdo->query("SELECT COUNT(*) FROM raw_samples")->fetchColumn();
check('Raw samples available', $rawCount > 0, $rawCount . ' rows');

// Roles presence
$roleCount = (int) $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
check('Roles configured', $roleCount > 0, $roleCount . ' roles');

// Check recent log errors
$logPath = $root . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $log = @file_get_contents($logPath);
    if ($log === false) {
        check('Laravel log readable', false);
    } else {
        $hasErrors = stripos($log, 'ERROR') !== false || stripos($log, 'Exception') !== false;
        check('Laravel log clean (no ERROR/Exception)', !$hasErrors, $hasErrors ? 'found' : 'clean', 'WARN');
    }
} else {
    check('Laravel log exists', false);
}

// PDF export dependency
$dompdfExists = file_exists($root . '/vendor/barryvdh/laravel-dompdf');
check('Dompdf installed', $dompdfExists);

// Monthly report templates
check('Monthly report view', file_exists($root . '/resources/views/pages/monthly-report.blade.php'));
check('Monthly report print view', file_exists($root . '/resources/views/pages/monthly-report-print.blade.php'));

echo PHP_EOL . "Deploy checklist finished." . PHP_EOL;
