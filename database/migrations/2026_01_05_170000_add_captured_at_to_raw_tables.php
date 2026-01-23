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
		Schema::table('raw_samples', function (Blueprint $table) {
			$table->timestamp('captured_at')->nullable();
		});
		Schema::table('raw_batches', function (Blueprint $table) {
			$table->timestamp('captured_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('raw_samples', function (Blueprint $table) {
			$table->dropColumn('captured_at');
		});
		Schema::table('raw_batches', function (Blueprint $table) {
			$table->dropColumn('captured_at');
		});
	}
};
