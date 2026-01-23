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

            // pengelompokan per batch (pakai ISO time dari ESP)
            $table->string('batch_id', 50);

            // waktu sample dalam millisecond
            $table->unsignedInteger('t_ms')->nullable();

            // nilai getaran 3 sumbu
            $table->float('ax_g', 10, 6)->nullable();
            $table->float('ay_g', 10, 6)->nullable();
            $table->float('az_g', 10, 6)->nullable();

            $table->timestamps();

            // index biar query cepat
            $table->index(['machine_id', 'batch_id']);
            $table->index(['machine_id', 't_ms']);

            // optional foreign key kalau tabel machines ada
            // $table->foreign('machine_id')->references('id')->on('machines')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_samples_3axis');
    }
};
