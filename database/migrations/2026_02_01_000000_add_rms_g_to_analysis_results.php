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
            if (!Schema::hasColumn('analysis_results', 'rms_g')) {
                $table->float('rms_g')->nullable()->after('rms');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('analysis_results')) {
            return;
        }

        Schema::table('analysis_results', function (Blueprint $table) {
            if (Schema::hasColumn('analysis_results', 'rms_g')) {
                $table->dropColumn('rms_g');
            }
        });
    }
};
