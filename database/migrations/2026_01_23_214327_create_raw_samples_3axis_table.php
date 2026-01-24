<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_samples_3axis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('machine_id');

            // ID batch (biar semua 256 sampel nyambung ke 1 batch)
            $table->string('batch_id', 50);

            // waktu sample dalam millisecond (sesuai data ESP)
            $table->unsignedInteger('t_ms')->nullable();

            // getaran 3 sumbu (float/double aman)
            $table->double('ax_g')->nullable();
            $table->double('ay_g')->nullable();
            $table->double('az_g')->nullable();

            $table->timestamps();

            // index supaya query cepat
            $table->index(['machine_id', 'batch_id']);
            $table->index(['machine_id', 't_ms']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_samples_3axis');
    }
};
