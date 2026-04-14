<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeBatchJob;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Iterasi2Test extends TestCase
{
    use RefreshDatabase;

    public function test_penentuan_status_mesin_normal_warning_anomali(): void
    {
        $user = $this->createSuperAdmin();

        $machineNormal = Machine::create([
            'name' => 'Machine Normal',
            'location' => 'L1',
            'threshold_warning' => 10.0,
            'threshold_critical' => 20.0,
            'is_active' => true,
        ]);
        $machineWarning = Machine::create([
            'name' => 'Machine Warning',
            'location' => 'L2',
            'threshold_warning' => 10.0,
            'threshold_critical' => 20.0,
            'is_active' => true,
        ]);
        $machineAnomaly = Machine::create([
            'name' => 'Machine Anomaly',
            'location' => 'L3',
            'threshold_warning' => 10.0,
            'threshold_critical' => 20.0,
            'is_active' => true,
        ]);

        $this->insertAnalysisRow((int) $machineNormal->id, 5.0);
        $this->insertAnalysisRow((int) $machineWarning->id, 15.0);
        $this->insertAnalysisRow((int) $machineAnomaly->id, 25.0);

        $response = $this->actingAs($user)->getJson('/api/machine-status');
        $response->assertOk()->assertJsonPath('success', true);

        $machines = collect($response->json('machines'));
        $this->assertSame(
            'NORMAL',
            $machines->firstWhere('id', $machineNormal->id)['status'] ?? null
        );
        $this->assertSame(
            'WARNING',
            $machines->firstWhere('id', $machineWarning->id)['status'] ?? null
        );
        $this->assertSame(
            'ANOMALY',
            $machines->firstWhere('id', $machineAnomaly->id)['status'] ?? null
        );
    }

    public function test_threshold_rms_per_mesin_digunakan_untuk_klasifikasi(): void
    {
        $user = $this->createSuperAdmin();

        $machineA = Machine::create([
            'name' => 'Machine A',
            'location' => 'L-A',
            'threshold_warning' => 5.0,
            'threshold_critical' => 8.0,
            'is_active' => true,
        ]);
        $machineB = Machine::create([
            'name' => 'Machine B',
            'location' => 'L-B',
            'threshold_warning' => 15.0,
            'threshold_critical' => 18.0,
            'is_active' => true,
        ]);

        // RMS sama, threshold berbeda -> status harus berbeda.
        $this->insertAnalysisRow((int) $machineA->id, 12.0);
        $this->insertAnalysisRow((int) $machineB->id, 12.0);

        $response = $this->actingAs($user)->getJson('/api/machine-status');
        $response->assertOk()->assertJsonPath('success', true);

        $machines = collect($response->json('machines'));
        $this->assertSame('ANOMALY', $machines->firstWhere('id', $machineA->id)['status'] ?? null);
        $this->assertSame('NORMAL', $machines->firstWhere('id', $machineB->id)['status'] ?? null);
    }

    public function test_integrasi_fft_menghasilkan_frekuensi_dan_spektrum(): void
    {
        $machine = Machine::create([
            'name' => 'Machine FFT',
            'location' => 'FFT-LAB',
            'threshold_warning' => 25.0,
            'threshold_critical' => 28.0,
            'is_active' => true,
        ]);

        $batchId = $this->insertRawBatch((int) $machine->id);
        $this->insertSineSamples((int) $machine->id, $batchId, 64, 50.0, 1000.0);

        $job = new AnalyzeBatchJob($batchId);
        $job->handle();

        $analysis = DB::table('analysis_results')
            ->where('machine_id', $machine->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($analysis);
        $this->assertTrue((float) ($analysis->dominant_freq_hz ?? 0) > 0);

        $fftResult = DB::table('fft_results')
            ->where('analysis_result_id', $analysis->id)
            ->first();

        $this->assertNotNull($fftResult);
        $this->assertNotEmpty(json_decode((string) $fftResult->frequencies, true));
        $this->assertNotEmpty(json_decode((string) $fftResult->amplitudes, true));
    }

    public function test_sistem_alert_menyimpan_dan_mendukung_acknowledge(): void
    {
        Cache::flush();
        $user = $this->createSuperAdmin();

        $machine = Machine::create([
            'name' => 'Machine Alert',
            'location' => 'AL-01',
            'threshold_warning' => 0.01,
            'threshold_critical' => 0.02,
            'is_active' => true,
        ]);

        $batchId = $this->insertRawBatch((int) $machine->id);
        $this->insertSineSamples((int) $machine->id, $batchId, 64, 80.0, 1000.0);

        $job = new AnalyzeBatchJob($batchId);
        $job->handle();

        $analysis = DB::table('analysis_results')
            ->where('machine_id', $machine->id)
            ->latest('id')
            ->first();
        $this->assertNotNull($analysis);
        $this->assertContains(strtoupper((string) ($analysis->condition_status ?? '')), ['WARNING', 'CRITICAL']);

        $this->assertDatabaseHas('notifications', [
            'machine_id' => $machine->id,
            'type' => 'ALERT',
        ]);

        $listResponse = $this->actingAs($user)->getJson('/api/alert-management/alerts');
        $listResponse->assertOk()->assertJsonPath('success', true);

        $firstAlertId = data_get($listResponse->json(), 'data.data.0.id');
        $this->assertNotNull($firstAlertId);

        $ackResponse = $this->actingAs($user)
            ->postJson("/api/alert-management/alerts/{$firstAlertId}/acknowledge", [
                'notes' => 'Ditangani oleh teknisi.',
            ]);

        $ackResponse->assertOk()->assertJsonPath('success', true);
        $this->assertTrue((bool) Cache::get("alert_ack_{$firstAlertId}", false));
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
        ]);
    }

    private function insertAnalysisRow(int $machineId, float $rms): void
    {
        $row = [
            'name' => "Analysis {$machineId}",
            'machine_id' => $machineId,
            'rms' => $rms,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('analysis_results', 'condition_status')) {
            $row['condition_status'] = 'NORMAL';
        }
        if (Schema::hasColumn('analysis_results', 'status')) {
            $row['status'] = 'done';
        }
        if (Schema::hasColumn('analysis_results', 'peak_amp')) {
            $row['peak_amp'] = $rms;
        }
        if (Schema::hasColumn('analysis_results', 'dominant_freq_hz')) {
            $row['dominant_freq_hz'] = 0.0;
        }

        DB::table('analysis_results')->insert($row);
    }

    private function insertRawBatch(int $machineId): int
    {
        $row = [
            'name' => 'Batch Iterasi2',
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

    private function insertSineSamples(int $machineId, int $batchId, int $n, float $freqHz, float $sampleRateHz): void
    {
        $rows = [];
        $dt = 1.0 / $sampleRateHz;

        for ($i = 0; $i < $n; $i++) {
            $t = $i * $dt;
            $ax = sin(2 * M_PI * $freqHz * $t);

            $row = [
                'name' => "Sample {$i}",
                'machine_id' => $machineId,
                'az_g' => 0.0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('raw_samples', 'batch_id')) {
                $row['batch_id'] = (string) $batchId;
            }
            if (Schema::hasColumn('raw_samples', 'raw_batch_id')) {
                $row['raw_batch_id'] = $batchId;
            }
            if (Schema::hasColumn('raw_samples', 't_ms')) {
                $row['t_ms'] = (int) round($i * 1000.0 / $sampleRateHz);
            }
            if (Schema::hasColumn('raw_samples', 'ax_g')) {
                $row['ax_g'] = $ax;
            }
            if (Schema::hasColumn('raw_samples', 'ay_g')) {
                $row['ay_g'] = 0.0;
            }
            if (Schema::hasColumn('raw_samples', 'temperature_c')) {
                $row['temperature_c'] = 30.0;
            }
            if (Schema::hasColumn('raw_samples', 'captured_at')) {
                $row['captured_at'] = now();
            }

            $rows[] = $row;
        }

        DB::table('raw_samples')->insert($rows);
    }
}

