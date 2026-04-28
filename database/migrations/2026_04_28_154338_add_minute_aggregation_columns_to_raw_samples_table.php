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
            if (!Schema::hasColumn('raw_samples', 'summary_count')) {
                $table->unsignedInteger('summary_count')->nullable()->after('temperature_c');
            }
            if (!Schema::hasColumn('raw_samples', 'summary_value_count')) {
                $table->unsignedInteger('summary_value_count')->nullable()->after('summary_count');
            }
            if (!Schema::hasColumn('raw_samples', 'summary_sum_ax')) {
                $table->double('summary_sum_ax')->nullable()->after('summary_value_count');
            }
            if (!Schema::hasColumn('raw_samples', 'summary_sum_ay')) {
                $table->double('summary_sum_ay')->nullable()->after('summary_sum_ax');
            }
            if (!Schema::hasColumn('raw_samples', 'summary_sum_az')) {
                $table->double('summary_sum_az')->nullable()->after('summary_sum_ay');
            }
            if (!Schema::hasColumn('raw_samples', 'summary_sum_value')) {
                $table->double('summary_sum_value')->nullable()->after('summary_sum_az');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_samples', function (Blueprint $table) {
            $dropColumns = [];
            foreach ([
                'summary_count',
                'summary_value_count',
                'summary_sum_ax',
                'summary_sum_ay',
                'summary_sum_az',
                'summary_sum_value',
            ] as $column) {
                if (Schema::hasColumn('raw_samples', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
