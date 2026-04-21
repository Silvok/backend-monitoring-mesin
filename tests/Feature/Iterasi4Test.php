<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Iterasi4Test extends TestCase
{
    use RefreshDatabase;

    public function test_rbac_menolak_akses_laporan_bulanan_tanpa_permission_reports_view(): void
    {
        Role::create([
            'name' => 'Koordinator Limited',
            'slug' => 'koordinator_limited',
            'permissions' => ['dashboard.view'],
            'is_protected' => false,
        ]);

        $user = User::factory()->create([
            'role' => 'koordinator_limited',
        ]);

        $this->actingAs($user)
            ->get(route('monthly-report'))
            ->assertForbidden();
    }

    public function test_filter_mesin_laporan_bulanan_mengembalikan_ringkasan_sesuai_mesin_terpilih(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 20, 10, 0, 0));

        try {
            $user = $this->createSuperAdmin();
            $machineA = $this->createMachine('decanter_01');
            $machineB = $this->createMachine('decanter_02');

            $this->insertAnalysisRow((int) $machineA->id, 6.2, 'NORMAL', '2026-04-10 08:00:00');
            $this->insertAnalysisRow((int) $machineA->id, 14.7, 'WARNING', '2026-04-12 09:00:00');
            $this->insertAnalysisRow((int) $machineB->id, 22.1, 'CRITICAL', '2026-04-14 10:00:00');
            $this->insertAnalysisRow((int) $machineA->id, 19.0, 'WARNING', '2026-03-29 11:00:00');

            $response = $this->actingAs($user)->get(route('monthly-report', [
                'month' => '2026-04',
                'machine_id' => (string) $machineA->id,
            ]));

            $response->assertOk()
                ->assertViewIs('pages.monthly-report')
                ->assertViewHas('selectedMachine', (string) $machineA->id)
                ->assertViewHas('summary', function (array $summary): bool {
                    return ($summary['total'] ?? null) === 2
                        && ($summary['normal'] ?? null) === 1
                        && ($summary['warning'] ?? null) === 1
                        && ($summary['critical'] ?? null) === 0;
                });
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_login_berhasil_memperbarui_last_login_at_pengguna(): void
    {
        $user = User::factory()->create([
            'email' => 'iterasi4@example.com',
            'password' => 'password',
            'last_login_at' => null,
        ]);

        $this->post('/login', [
            'email' => 'iterasi4@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
        ]);
    }

    private function createMachine(string $name): Machine
    {
        return Machine::create([
            'name' => $name,
            'location' => 'LAB',
            'threshold_warning' => 10.0,
            'threshold_critical' => 20.0,
            'is_active' => true,
        ]);
    }

    private function insertAnalysisRow(int $machineId, float $rms, string $conditionStatus, string $createdAt): void
    {
        $timestamp = Carbon::parse($createdAt);

        $row = [
            'name' => "Analysis {$machineId}",
            'machine_id' => $machineId,
            'rms' => $rms,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];

        if (Schema::hasColumn('analysis_results', 'condition_status')) {
            $row['condition_status'] = $conditionStatus;
        }
        if (Schema::hasColumn('analysis_results', 'status')) {
            $row['status'] = 'done';
        }
        if (Schema::hasColumn('analysis_results', 'peak_amp')) {
            $row['peak_amp'] = $rms;
        }
        if (Schema::hasColumn('analysis_results', 'dominant_freq_hz')) {
            $row['dominant_freq_hz'] = 50.0;
        }

        DB::table('analysis_results')->insert($row);
    }
}
