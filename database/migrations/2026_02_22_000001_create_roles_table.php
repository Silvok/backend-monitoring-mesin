<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function defaultRoles(): array
    {
        return [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'permissions' => ['*'],
                'is_protected' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'permissions' => [
                    'dashboard.view',
                    'realtime.view',
                    'monitoring.view',
                    'parameter.view',
                    'analysis.view',
                    'reports.view',
                    'fft.view',
                    'alerts.view',
                    'alerts.ack',
                    'alert_management.view',
                    'alert_management.stats',
                    'alert_management.history',
                    'alert_management.resolve',
                    'alert_management.bulk_ack',
                    'alert_management.thresholds',
                    'alert_management.notifications',
                    'export.alerts',
                    'settings.view',
                    'settings.update',
                    'user_management.view',
                    'user_management.create',
                    'user_management.edit',
                    'user_management.delete',
                    'user_management.reset_password',
                ],
                'is_protected' => true,
            ],
            [
                'name' => 'Koordinator',
                'slug' => 'koordinator',
                'permissions' => [
                    'dashboard.view',
                    'realtime.view',
                    'monitoring.view',
                    'parameter.view',
                    'analysis.view',
                    'reports.view',
                    'fft.view',
                    'alerts.view',
                    'alerts.ack',
                    'alert_management.view',
                    'alert_management.stats',
                    'alert_management.history',
                ],
                'is_protected' => true,
            ],
        ];
    }

    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->json('permissions')->nullable();
                $table->boolean('is_protected')->default(false);
                $table->timestamps();
            });
        }

        // Seed/update default roles safely, even if table already exists.
        $now = now();
        foreach ($this->defaultRoles() as $role) {
            $exists = DB::table('roles')->where('slug', $role['slug'])->exists();
            $payload = [
                'name' => $role['name'],
                'permissions' => json_encode($role['permissions'], JSON_UNESCAPED_UNICODE),
                'is_protected' => $role['is_protected'],
                'updated_at' => $now,
            ];

            if ($exists) {
                DB::table('roles')->where('slug', $role['slug'])->update($payload);
                continue;
            }

            DB::table('roles')->insert([
                ...$payload,
                'slug' => $role['slug'],
                'created_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
