<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class UpdateAnalysisResultsConditionStatusThreshold extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update status berdasarkan threshold baru
        DB::table('analysis_results')->where('rms', '<', 0.05)
            ->update(['condition_status' => 'NORMAL']);
        DB::table('analysis_results')->where('rms', '>=', 0.05)->where('rms', '<', 0.15)
            ->update(['condition_status' => 'WARNING']);
        DB::table('analysis_results')->where('rms', '>=', 0.15)
            ->update(['condition_status' => 'ALERT']);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Tidak ada rollback, karena data sudah diubah
    }
}
