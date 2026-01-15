<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fft_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analysis_result_id');
            $table->json('frequencies'); // array of frequency values
            $table->json('amplitudes');  // array of amplitude values
            $table->timestamps();

            $table->foreign('analysis_result_id')
                ->references('id')->on('analysis_results')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fft_results');
    }
};
