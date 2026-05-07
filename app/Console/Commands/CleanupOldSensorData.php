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

        // Raw data: simpan pendek
        $this->deleteOldData('raw_samples', 1);
        $this->deleteOldData('raw_samples_3axis', 1);

        // Batch dan temperature readings: simpan beberapa hari
        $this->deleteOldData('raw_batches', 7);
        $this->deleteOldData('temperature_readings', 7);

        // Hasil analisis: simpan lebih lama, contoh 180 hari / 6 bulan
        $this->deleteOldData('analysis_results', 180);

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
