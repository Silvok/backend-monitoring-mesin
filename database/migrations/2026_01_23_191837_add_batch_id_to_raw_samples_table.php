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
            $table->string('batch_id', 50)->nullable()->after('machine_id');
            $table->index(['machine_id', 'batch_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        Schema::table('raw_samples', function (Blueprint $table) {
            $table->dropIndex(['machine_id', 'batch_id']);
            $table->dropColumn('batch_id');
        });
    }

};
