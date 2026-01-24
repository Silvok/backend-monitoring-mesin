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
        Schema::table('raw_samples', function (Blueprint $table) {
            // Tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('raw_samples', 't_ms')) {
                $table->unsignedInteger('t_ms')->nullable()->after('batch_id');
            }
            if (!Schema::hasColumn('raw_samples', 'ax_g')) {
                $table->float('ax_g', 10, 6)->nullable()->after('t_ms');
            }
            if (!Schema::hasColumn('raw_samples', 'ay_g')) {
                $table->float('ay_g', 10, 6)->nullable()->after('ax_g');
            }
            if (!Schema::hasColumn('raw_samples', 'az_g')) {
                $table->float('az_g', 10, 6)->nullable()->after('ay_g');
            }
            // index supaya query cepat
            $existingIndexes = collect(\DB::select("SHOW INDEX FROM raw_samples"))->pluck('Key_name');
            if (!$existingIndexes->contains('raw_samples_machine_id_batch_id_index')) {
                $table->index(['machine_id', 'batch_id']);
            }
            if (!$existingIndexes->contains('raw_samples_machine_id_t_ms_index')) {
                $table->index(['machine_id', 't_ms']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_samples', function (Blueprint $table) {

            $table->dropIndex(['machine_id', 'batch_id']);
            $table->dropIndex(['machine_id', 't_ms']);

            $table->dropColumn(['t_ms', 'ax_g', 'ay_g', 'az_g']);
        });
    }
};
