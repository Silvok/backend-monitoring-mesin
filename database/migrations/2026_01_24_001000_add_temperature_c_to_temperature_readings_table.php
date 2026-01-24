<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('temperature_readings', function (Blueprint $table) {
            $table->float('temperature_c')->nullable();
        });
    }

    public function down()
    {
        Schema::table('temperature_readings', function (Blueprint $table) {
            $table->dropColumn('temperature_c');
        });
    }
};
