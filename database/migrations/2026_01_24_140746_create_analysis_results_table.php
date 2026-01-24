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
        if (Schema::hasTable('analysis_results')) {
            return;
        }

        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();

            // Relasi ke raw_batches
            $table->foreignId('raw_batch_id')
                  ->constrained('raw_batches')
                  ->cascadeOnDelete();

            // RMS per axis
            $table->double('rms_x');
            $table->double('rms_y');
            $table->double('rms_z');

            // Status kondisi mesin
            $table->string('status', 50);

            $table->timestamps();

            // Optional index untuk query dashboard
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        Schema::table('raw_samples', function (Blueprint $table) {
            if (Schema::hasColumn('raw_samples', 'batch_id')) {
                $table->dropColumn('batch_id');
            }
        });
    }

};
