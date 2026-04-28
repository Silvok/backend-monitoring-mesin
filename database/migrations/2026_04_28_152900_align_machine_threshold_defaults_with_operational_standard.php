<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite' && Schema::hasColumn('machines', 'threshold_warning')) {
            DB::statement("
                ALTER TABLE `machines`
                MODIFY `threshold_warning` DECIMAL(5,2) NOT NULL DEFAULT 25.00
                COMMENT 'Warning threshold in mm/s (Operational standard)'
            ");
        }

        if ($driver !== 'sqlite' && Schema::hasColumn('machines', 'threshold_critical')) {
            DB::statement("
                ALTER TABLE `machines`
                MODIFY `threshold_critical` DECIMAL(5,2) NOT NULL DEFAULT 28.00
                COMMENT 'Critical threshold in mm/s (Operational standard)'
            ");
        }

        DB::table('machines')
            ->where(function ($query) {
                $query->whereNull('threshold_warning')
                    ->orWhereRaw('ABS(threshold_warning - 2.8) < 0.0001');
            })
            ->update(['threshold_warning' => 25.00]);

        DB::table('machines')
            ->where(function ($query) {
                $query->whereNull('threshold_critical')
                    ->orWhereRaw('ABS(threshold_critical - 7.1) < 0.0001');
            })
            ->update(['threshold_critical' => 28.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite' && Schema::hasColumn('machines', 'threshold_warning')) {
            DB::statement("
                ALTER TABLE `machines`
                MODIFY `threshold_warning` DECIMAL(5,2) NOT NULL DEFAULT 2.80
                COMMENT 'Warning threshold in mm/s (ISO 10816-3)'
            ");
        }

        if ($driver !== 'sqlite' && Schema::hasColumn('machines', 'threshold_critical')) {
            DB::statement("
                ALTER TABLE `machines`
                MODIFY `threshold_critical` DECIMAL(5,2) NOT NULL DEFAULT 7.10
                COMMENT 'Critical threshold in mm/s (ISO 10816-3)'
            ");
        }
    }
};
