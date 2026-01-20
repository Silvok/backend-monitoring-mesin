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
        Schema::table('machines', function (Blueprint $table) {
            $table->decimal('threshold_warning', 5, 2)->default(1.8)->after('location')
                ->comment('Warning threshold in mm/s (ISO 10816-3)');
            $table->decimal('threshold_critical', 5, 2)->default(4.5)->after('threshold_warning')
                ->comment('Critical threshold in mm/s (ISO 10816-3)');
            $table->decimal('motor_power_hp', 8, 2)->nullable()->after('threshold_critical')
                ->comment('Motor power in HP');
            $table->integer('motor_rpm')->nullable()->after('motor_power_hp')
                ->comment('Motor RPM');
            $table->string('iso_class', 20)->default('Class I')->after('motor_rpm')
                ->comment('ISO 10816-3 Class (I, II, III, IV)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['threshold_warning', 'threshold_critical', 'motor_power_hp', 'motor_rpm', 'iso_class']);
        });
    }
};
