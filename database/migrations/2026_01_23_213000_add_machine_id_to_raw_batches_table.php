<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('raw_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('machine_id')->nullable()->after('id');
        });
    }
    public function down() {
        Schema::table('raw_batches', function (Blueprint $table) {
            $table->dropColumn('machine_id');
        });
    }
};
