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
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';
$dsn = "mysql:host=$host;port=$port;dbname=$db";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$warning = 25.0;
$critical = 28.0;
$updated = $pdo->exec("UPDATE machines SET threshold_warning=$warning, threshold_critical=$critical");
$recalc = $pdo->exec("UPDATE analysis_results ar JOIN machines m ON m.id = ar.machine_id SET ar.condition_status = CASE WHEN ar.rms >= m.threshold_critical THEN 'CRITICAL' WHEN ar.rms >= m.threshold_warning THEN 'WARNING' ELSE 'NORMAL' END");
echo "updated_machines=$updated\n";
echo "recomputed_results=$recalc\n";
?>
