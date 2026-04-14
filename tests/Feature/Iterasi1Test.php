<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ESPController;
use App\Jobs\AnalyzeBatchJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use ReflectionMethod;
use Tests\TestCase;

class Iterasi1Test extends TestCase
{
    use RefreshDatabase;

    public function test_login_pengguna(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_validasi_login_dengan_kredensial_salah(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'salah-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_pengguna(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_api_listener_menerima_data_sensor(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/esp-data', $this->sensorPayload(1));

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        Queue::assertPushed(AnalyzeBatchJob::class);
    }

    public function test_penyimpanan_data_sensor(): void
    {
        Queue::fake();

        $this->postJson('/api/esp-data', $this->sensorPayload(2))
            ->assertOk();

        $this->assertDatabaseHas('raw_batches', [
            'machine_id' => 2,
        ]);

        $sample = DB::table('raw_samples')
            ->where('machine_id', 2)
            ->latest('id')
            ->first();

        $this->assertNotNull($sample);
        $this->assertNotNull($sample->ax_g);
        $this->assertNotNull($sample->ay_g);
        $this->assertNotNull($sample->az_g);
    }

    public function test_perhitungan_rms(): void
    {
        $controller = new ESPController();
        $method = new ReflectionMethod($controller, 'buildPrecomputedMetricsFromWindow');
        $method->setAccessible(true);

        $values = [1.0, 2.0, 3.0, 4.0];
        $times = [0, 1, 2, 3];

        /** @var array<string, mixed> $metrics */
        $metrics = $method->invoke($controller, $values, $times);

        $expectedRms = sqrt((1 + 4 + 9 + 16) / 4); // sqrt(7.5)
        $this->assertSame(4, $metrics['sample_count']);
        $this->assertEqualsWithDelta($expectedRms, (float) $metrics['rms_g'], 0.00001);
    }

    private function sensorPayload(int $machineId): array
    {
        return [
            'machine_id' => $machineId,
            'captured_at' => now()->toDateTimeString(),
            'temperature_c' => 31.2,
            'data' => [
                [0, 0.10, 0.20, 0.30, 0.0],
                [10, 0.20, 0.10, 0.30, 0.0],
                [20, 0.15, 0.25, 0.35, 0.0],
            ],
        ];
    }
}

