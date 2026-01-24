<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temperature_readings', function (Blueprint $table) {
            if (!Schema::hasColumn('temperature_readings', 'machine_id')) {
                $table->unsignedBigInteger('machine_id')->nullable()->after('id');
                $table->index(['machine_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('temperature_readings', function (Blueprint $table) {
            if (Schema::hasColumn('temperature_readings', 'machine_id')) {
                $table->dropIndex(['machine_id']);
                $table->dropColumn('machine_id');
            }
        });
    }
};
