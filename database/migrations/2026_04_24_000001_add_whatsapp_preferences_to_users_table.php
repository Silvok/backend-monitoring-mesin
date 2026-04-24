<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 20)->nullable()->after('email');
            });
        }

        if (!Schema::hasColumn('users', 'wa_notification_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('wa_notification_enabled')->default(true)->after('phone');
            });
        }

        DB::table('users')->update([
            'phone' => '083183061372',
            'wa_notification_enabled' => 1,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'wa_notification_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('wa_notification_enabled');
            });
        }
    }
};

