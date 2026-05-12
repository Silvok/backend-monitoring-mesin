<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldSensorData extends Command
{
    protected $signature = 'sensor:cleanup-old-data';

    protected $description = 'Clean old raw sensor data while keeping analysis results and monthly reports longer.';

    public function handle(): int
    {
        if (! app()->environment('production')) {
            $this->warn('Cleanup skipped because app is not running in production.');
            return self::SUCCESS;
        }

        $this->info('Starting old sensor data cleanup...');

        // Raw data retention (days) - configurable via env.
        $rawDays = max(1, (int) env('SENSOR_RETENTION_RAW_SAMPLES_DAYS', 1));
        $raw3AxisDays = max(1, (int) env('SENSOR_RETENTION_RAW_3AXIS_DAYS', $rawDays));

        // Batch and temperature retention (days).
        $batchDays = max(1, (int) env('SENSOR_RETENTION_RAW_BATCHES_DAYS', 7));
        $temperatureDays = max(1, (int) env('SENSOR_RETENTION_TEMPERATURE_DAYS', 7));

        // Analysis result retention (days).
        $analysisDays = max(1, (int) env('SENSOR_RETENTION_ANALYSIS_DAYS', 180));

        $this->deleteOldData('raw_samples', $rawDays);
        $this->deleteOldData('raw_samples_3axis', $raw3AxisDays);
        $this->deleteOldData('raw_batches', $batchDays);
        $this->deleteOldData('temperature_readings', $temperatureDays);
        $this->deleteOldData('analysis_results', $analysisDays);

        $this->info('Old sensor data cleanup completed.');

        return self::SUCCESS;
    }

    private function deleteOldData(string $table, int $days): void
    {
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            $this->warn("Table {$table} does not exist. Skipped.");
            return;
        }

        $deleted = DB::table($table)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$deleted} old rows from {$table} older than {$days} day(s).");
    }
}
