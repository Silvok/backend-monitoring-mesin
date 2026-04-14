<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Iterasi3Test extends TestCase
{
    use RefreshDatabase;

    public function test_filter_data_historis_mengembalikan_data_sesuai_rentang_waktu(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 12, 0, 0));

        try {
            $user = $this->createSuperAdmin();
            $machine = $this->createMachine('Machine Hist');
            $batchId = $this->insertRawBatch((int) $machine->id);

            $now = now();
            $this->insertSensorSample((int) $machine->id, $batchId, $now->copy()->subHours(10), 0.1111, 0.2222, 0.3333, 29.1);
            $this->insertSensorSample((int) $machine->id, $batchId, $now->copy()->subHour(), 0.4444, 0.5555, 0.6666, 30.2);

            $response = $this->actingAs($user)->getJson(
                "/api/machine/{$machine->id}/historical-data?date={$now->format('Y-m-d')}&hours=3"
            );

            $response->assertOk()->assertJsonPath('success', true);
            $response->assertJsonCount(1, 'sensor_data');
            $response->assertJsonPath('sensor_data.0.acceleration_x', 0.4444);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_tabel_riwayat_data_sensor_mendukung_pagination(): void
    {
        $user = $this->createSuperAdmin();
        $machine = $this->createMachine('Machine History');
        $batchId = $this->insertRawBatch((int) $machine->id);

        $now = now();
        for ($i = 0; $i < 25; $i++) {
            $v = (float) $i / 100;
            $this->insertSensorSample(
                (int) $machine->id,
                $batchId,
                $now->copy()->subMinutes($i),
                $v,
                $v + 0.1,
                $v + 0.2,
                30.0
            );
        }

        $response = $this->actingAs($user)->getJson(
            "/api/machine/{$machine->id}/sensor-history?date={$now->format('Y-m-d')}&hours=24&page=1&per_page=20"
        );

        $response->assertOk()->assertJsonPath('success', true);
        $response->assertJsonPath('pagination.total', 25);
        $response->assertJsonPath('pagination.current_page', 1);
        $response->assertJsonPath('pagination.last_page', 2);
        $response->assertJsonPath('pagination.per_page', 20);
        $response->assertJsonCount(20, 'data');
    }

    public function test_export_csv_riwayat_sensor_berhasil_diunduh(): void
    {
        $user = $this->createSuperAdmin();
        $machine = $this->createMachine('Machine Export');
        $batchId = $this->insertRawBatch((int) $machine->id);

        $sampleTime = now()->subMinutes(15);
        $this->insertSensorSample((int) $machine->id, $batchId, $sampleTime, 0.5, 0.4, 0.3, 31.7);

        $response = $this->actingAs($user)->get(
            "/api/machine/{$machine->id}/sensor-history/export?date=" . now()->format('Y-m-d') . "&hours=24"
        );

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Waktu,Akselerasi_X_G,Akselerasi_Y_G,Akselerasi_Z_G,Suhu_C,RMS_G', $csv);
        $this->assertStringContainsString('0.5', $csv);
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
            'threshold_warning' => 25.0,
            'threshold_critical' => 28.0,
            'is_active' => true,
        ]);
    }

    private function insertRawBatch(int $machineId): int
    {
        $row = [
            'name' => 'Batch Iterasi3',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('raw_batches', 'machine_id')) {
            $row['machine_id'] = $machineId;
        }
        if (Schema::hasColumn('raw_batches', 'captured_at')) {
            $row['captured_at'] = now();
        }
        if (Schema::hasColumn('raw_batches', 'batch_time')) {
            $row['batch_time'] = now();
        }

        return (int) DB::table('raw_batches')->insertGetId($row);
    }

    private function insertSensorSample(
        int $machineId,
        int $batchId,
        $createdAt,
        float $ax,
        float $ay,
        float $az,
        float $temperature
    ): void {
        $row = [
            'name' => 'Sample Iterasi3',
            'machine_id' => $machineId,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        if (Schema::hasColumn('raw_samples', 'batch_id')) {
            $row['batch_id'] = (string) $batchId;
        }
        if (Schema::hasColumn('raw_samples', 'raw_batch_id')) {
            $row['raw_batch_id'] = $batchId;
        }
        if (Schema::hasColumn('raw_samples', 'ax_g')) {
            $row['ax_g'] = $ax;
        }
        if (Schema::hasColumn('raw_samples', 'ay_g')) {
            $row['ay_g'] = $ay;
        }
        if (Schema::hasColumn('raw_samples', 'az_g')) {
            $row['az_g'] = $az;
        }
        if (Schema::hasColumn('raw_samples', 'temperature_c')) {
            $row['temperature_c'] = $temperature;
        }
        if (Schema::hasColumn('raw_samples', 'captured_at')) {
            $row['captured_at'] = $createdAt;
        }
        if (Schema::hasColumn('raw_samples', 't_ms')) {
            $row['t_ms'] = 0;
        }

        DB::table('raw_samples')->insert($row);
    }
}
