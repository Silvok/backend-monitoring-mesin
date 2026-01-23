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

            // t_ms = waktu dalam millisecond (integer)
            // contoh: 13249 atau 25510
            $table->unsignedInteger('t_ms')->nullable()->after('batch_id');

            // ax, ay, az dalam satuan g (float)
            $table->float('ax_g', 10, 6)->nullable()->after('t_ms');
            $table->float('ay_g', 10, 6)->nullable()->after('ax_g');
            $table->float('az_g', 10, 6)->nullable()->after('ay_g');

            // index supaya query cepat
            $table->index(['machine_id', 'batch_id']);
            $table->index(['machine_id', 't_ms']);
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
