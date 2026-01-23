<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('analysis_results', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->unsignedBigInteger('machine_id')->nullable();
			$table->float('rms')->nullable();
			$table->text('result')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('analysis_results');
	}
};
