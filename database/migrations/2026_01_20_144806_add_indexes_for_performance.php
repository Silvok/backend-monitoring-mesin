<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite index for raw_samples (machine_id + created_at)
        Schema::table('raw_samples', function (Blueprint $table) {
            $table->index(['machine_id', 'created_at'], 'raw_samples_machine_created_idx');
        });

        // Add composite index for analysis_results (machine_id + created_at + condition_status)
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->index(['machine_id', 'created_at'], 'analysis_results_machine_created_idx');
            $table->index(['machine_id', 'condition_status', 'created_at'], 'analysis_results_machine_status_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_samples', function (Blueprint $table) {
            $table->dropIndex('raw_samples_machine_created_idx');
        });

        Schema::table('analysis_results', function (Blueprint $table) {
            $table->dropIndex('analysis_results_machine_created_idx');
            $table->dropIndex('analysis_results_machine_status_created_idx');
        });
    }
};
