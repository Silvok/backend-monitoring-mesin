<?php
$envPath = __DIR__.'/.env';
$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#')) continue;
    if (!str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);
    $value = trim($value, "\"'");
    $env[$key] = $value;
}
$host = $env['DB_HOST'] ?? '128.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';
$dsn = "mysql:host=$host;port=$port;dbname=$db";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$date = '2026-02-26';
$total = (int)$pdo->query("SELECT COUNT(*) FROM analysis_results WHERE DATE(created_at)='$date'")->fetchColumn();
if ($total === 0) { echo "count=0\n"; exit; }
$p95 = (int)ceil($total * 0.95);
$p99 = (int)ceil($total * 0.99);
$warning = $pdo->query("SELECT rms FROM analysis_results WHERE DATE(created_at)='$date' ORDER BY rms LIMIT 1 OFFSET ".($p95-1))->fetchColumn();
$critical = $pdo->query("SELECT rms FROM analysis_results WHERE DATE(created_at)='$date' ORDER BY rms LIMIT 1 OFFSET ".($p99-1))->fetchColumn();
$avg = $pdo->query("SELECT AVG(rms) FROM analysis_results WHERE DATE(created_at)='$date'")->fetchColumn();
$max = $pdo->query("SELECT MAX(rms) FROM analysis_results WHERE DATE(created_at)='$date'")->fetchColumn();
$min = $pdo->query("SELECT MIN(rms) FROM analysis_results WHERE DATE(created_at)='$date'")->fetchColumn();

echo "count=$total\n";
echo "avg=$avg\n";
echo "min=$min\n";
echo "max=$max\n";
echo "p95=$warning\n";
echo "p99=$critical\n";
?>

