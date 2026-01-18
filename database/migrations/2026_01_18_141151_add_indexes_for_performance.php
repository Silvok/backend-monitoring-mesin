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
        // Add indexes to analysis_results for faster queries
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->index('created_at', 'idx_analysis_created_at');
            $table->index('condition_status', 'idx_analysis_condition_status');
            $table->index('machine_id', 'idx_analysis_machine_id');
            $table->index('rms', 'idx_analysis_rms');
            $table->index(['machine_id', 'created_at'], 'idx_analysis_machine_created');
            $table->index(['condition_status', 'created_at'], 'idx_analysis_status_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->dropIndex('idx_analysis_created_at');
            $table->dropIndex('idx_analysis_condition_status');
            $table->dropIndex('idx_analysis_machine_id');
            $table->dropIndex('idx_analysis_rms');
            $table->dropIndex('idx_analysis_machine_created');
            $table->dropIndex('idx_analysis_status_created');
        });
    }
};
