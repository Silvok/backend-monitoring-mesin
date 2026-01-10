<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up() {
        // Konsistenkan threshold di database sesuai standar terbaru
        DB::table('analysis_results')->where('rms', '<', 0.7)
            ->update(['condition_status' => 'NORMAL']);
        DB::table('analysis_results')->where('rms', '>=', 0.7)->where('rms', '<', 1.8)
            ->update(['condition_status' => 'WARNING']);
        DB::table('analysis_results')->where('rms', '>=', 1.8)
            ->update(['condition_status' => 'ANOMALY']);
    }

    public function down() {
        // Tidak ada rollback untuk data migration
    }
};
