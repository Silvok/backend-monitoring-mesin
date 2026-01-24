<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('analysis_results')) {
            return;
        }

        Schema::table('analysis_results', function (Blueprint $table) {
            if (!Schema::hasColumn('analysis_results', 'peak_amp')) {
                $table->float('peak_amp')->nullable()->after('rms_g');
            }
            if (!Schema::hasColumn('analysis_results', 'dominant_freq_hz')) {
                $table->float('dominant_freq_hz')->nullable()->after('peak_amp');
            }
            if (!Schema::hasColumn('analysis_results', 'mean')) {
                $table->float('mean')->nullable()->after('dominant_freq_hz');
            }
            if (!Schema::hasColumn('analysis_results', 'std')) {
                $table->float('std')->nullable()->after('mean');
            }
            if (!Schema::hasColumn('analysis_results', 'fs_hz')) {
                $table->float('fs_hz')->nullable()->after('std');
            }
            if (!Schema::hasColumn('analysis_results', 'n')) {
                $table->unsignedInteger('n')->nullable()->after('fs_hz');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('analysis_results')) {
            return;
        }

        Schema::table('analysis_results', function (Blueprint $table) {
            $columns = ['peak_amp', 'dominant_freq_hz', 'mean', 'std', 'fs_hz', 'n'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('analysis_results', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
