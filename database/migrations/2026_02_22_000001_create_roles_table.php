<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('permissions')->nullable();
            $table->boolean('is_protected')->default(false);
            $table->timestamps();
        });

        // Seed default roles
        $now = now();
        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'permissions' => json_encode(['*']),
                'is_protected' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'permissions' => json_encode([
                    'dashboard.view',
                    'realtime.view',
                    'monitoring.view',
                    'parameter.view',
                    'analysis.view',
                    'alerts.view',
                    'alerts.ack',
                    'alert_management.view',
                    'alert_management.stats',
                    'alert_management.history',
                    'export.alerts',
                    'reports.view',
                    'user_management.view',
                    'user_management.create',
                    'user_management.edit',
                    'user_management.delete',
                ]),
                'is_protected' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Koordinator',
                'slug' => 'koordinator',
                'permissions' => json_encode([
                    'dashboard.view',
                    'realtime.view',
                    'monitoring.view',
                    'parameter.view',
                    'analysis.view',
                    'alerts.view',
                    'alerts.ack',
                    'alert_management.view',
                    'alert_management.stats',
                    'alert_management.history',
                ]),
                'is_protected' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
