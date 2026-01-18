<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;
use App\Models\AnalysisResult;
use App\Models\FFTResult;

class FFTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all machines
        $machines = Machine::all();

        if ($machines->isEmpty()) {
            $this->command->info('No machines found. Creating sample machines...');

            // Create sample machines if none exist
            $machines = collect([
                Machine::create(['name' => 'Motor Pump A', 'location' => 'Plant 1', 'type' => 'Pump']),
                Machine::create(['name' => 'Compressor B', 'location' => 'Plant 1', 'type' => 'Compressor']),
                Machine::create(['name' => 'Fan Unit C', 'location' => 'Plant 2', 'type' => 'Fan']),
            ]);
        }

        $this->command->info('Creating FFT data for ' . $machines->count() . ' machines...');

        // Get max batch_id to avoid unique constraint violation
        $maxBatchId = AnalysisResult::max('batch_id') ?? 0;
        $batchCounter = $maxBatchId + 1000; // Start from safe offset

        foreach ($machines as $machine) {
            // Create 3 analysis results with FFT data for each machine
            for ($i = 0; $i < 3; $i++) {
                $rms = $this->generateRMS();
                $conditionStatus = $this->getConditionStatus($rms);

                // Create analysis result
                $analysis = AnalysisResult::create([
                    'batch_id' => $batchCounter++,
                    'machine_id' => $machine->id,
                    'fs_hz' => 1000.0, // Sample frequency
                    'n' => 1024,       // Number of samples
                    'rms' => $rms,
                    'peak_amp' => $rms * (1.2 + mt_rand(0, 50) / 100),
                    'dominant_freq_hz' => $this->getBaseFreq($machine->type ?? 'General'),
                    'mean' => $rms * 0.8,
                    'std' => $rms * 0.1,
                    'condition_status' => $conditionStatus,
                    'created_at' => now()->subMinutes(($i + 1) * 30),
                    'updated_at' => now()->subMinutes(($i + 1) * 30),
                ]);

                // Generate FFT data
                $fftData = $this->generateFFTData($machine->type ?? 'General', $conditionStatus);

                // Create FFT result
                FFTResult::create([
                    'analysis_result_id' => $analysis->id,
                    'frequencies' => $fftData['frequencies'],
                    'amplitudes' => $fftData['amplitudes'],
                ]);

                $this->command->info("Created FFT for {$machine->name} - Analysis #{$analysis->id}");
            }
        }

        $this->command->info('FFT seeding completed!');
    }

    /**
     * Get base frequency for machine type
     */
    private function getBaseFreq(string $machineType): float
    {
        return match ($machineType) {
            'Pump' => 50.0,
            'Compressor' => 60.0,
            'Fan' => 25.0,
            default => 50.0
        };
    }

    /**
     * Generate random RMS value
     */
    private function generateRMS(): float
    {
        $ranges = [
            [0.1, 0.5],   // Good - 50%
            [0.5, 1.5],   // Normal - 30%
            [1.5, 3.0],   // Warning - 15%
            [3.0, 6.0],   // Critical - 5%
        ];

        $rand = mt_rand(1, 100);
        if ($rand <= 50) {
            $range = $ranges[0];
        } elseif ($rand <= 80) {
            $range = $ranges[1];
        } elseif ($rand <= 95) {
            $range = $ranges[2];
        } else {
            $range = $ranges[3];
        }

        return round($range[0] + mt_rand(0, 1000) / 1000 * ($range[1] - $range[0]), 4);
    }

    /**
     * Get condition status based on RMS
     */
    private function getConditionStatus(float $rms): string
    {
        if ($rms < 0.5) return 'good';
        if ($rms < 1.5) return 'normal';
        if ($rms < 3.0) return 'warning';
        return 'critical';
    }

    /**
     * Generate realistic FFT data
     */
    private function generateFFTData(string $machineType, string $condition): array
    {
        $frequencies = [];
        $amplitudes = [];

        // Determine base parameters based on machine type
        $baseFreq = match ($machineType) {
            'Pump' => 50,      // 50 Hz (3000 RPM motor)
            'Compressor' => 60, // 60 Hz
            'Fan' => 25,       // 25 Hz (1500 RPM fan)
            default => 50
        };

        // Generate frequency range (0 to 500 Hz)
        $maxFreq = 500;
        $step = 0.5;
        $numPoints = (int)($maxFreq / $step);

        for ($i = 0; $i <= $numPoints; $i++) {
            $freq = $i * $step;
            $frequencies[] = $freq;

            // Calculate amplitude with realistic FFT characteristics
            $amp = $this->calculateAmplitude($freq, $baseFreq, $condition);
            $amplitudes[] = $amp;
        }

        return [
            'frequencies' => $frequencies,
            'amplitudes' => $amplitudes,
        ];
    }

    /**
     * Calculate amplitude for a frequency
     */
    private function calculateAmplitude(float $freq, float $baseFreq, string $condition): float
    {
        // Base noise floor
        $noise = 0.0001 + (mt_rand(0, 100) / 1000000);

        // Amplitude multiplier based on condition
        $conditionMultiplier = match ($condition) {
            'good' => 1.0,
            'normal' => 1.5,
            'warning' => 2.5,
            'critical' => 4.0,
            default => 1.0
        };

        // Check for fundamental frequency and harmonics
        $harmonics = [1, 2, 3, 4, 5, 6, 7, 8]; // 1x, 2x, 3x, etc.

        foreach ($harmonics as $harmonic) {
            $harmonicFreq = $baseFreq * $harmonic;
            $tolerance = 0.5; // Hz tolerance

            if (abs($freq - $harmonicFreq) < $tolerance) {
                // Create peak at harmonic frequency
                $peakAmplitude = $this->getHarmonicAmplitude($harmonic, $conditionMultiplier);

                // Add gaussian-like shape around peak
                $distance = abs($freq - $harmonicFreq);
                $width = 1.0; // Peak width
                $gaussianFactor = exp(-($distance * $distance) / (2 * $width * $width));

                return $peakAmplitude * $gaussianFactor * $conditionMultiplier + $noise;
            }
        }

        // Check for sub-harmonics (bearing defects)
        if ($condition === 'warning' || $condition === 'critical') {
            $subHarmonics = [0.5, 0.33, 0.25]; // Sub-harmonics
            foreach ($subHarmonics as $sub) {
                $subFreq = $baseFreq * $sub;
                if (abs($freq - $subFreq) < 0.5) {
                    return 0.005 * $conditionMultiplier + $noise;
                }
            }
        }

        // Check for bearing frequencies (BPFO, BPFI, BSF, FTF)
        if ($condition === 'warning' || $condition === 'critical') {
            $bearingFreqs = [
                $baseFreq * 3.56,  // BPFO
                $baseFreq * 5.12,  // BPFI
                $baseFreq * 2.32,  // BSF
                $baseFreq * 0.38,  // FTF
            ];

            foreach ($bearingFreqs as $bf) {
                if (abs($freq - $bf) < 1.0) {
                    return 0.008 * $conditionMultiplier + $noise;
                }
            }
        }

        // Background vibration (random low-level noise)
        return $noise;
    }

    /**
     * Get amplitude for harmonic number
     */
    private function getHarmonicAmplitude(int $harmonic, float $multiplier): float
    {
        // Typical harmonic amplitude decay
        $baseAmplitudes = [
            1 => 0.05,   // 1x - highest
            2 => 0.02,   // 2x - misalignment
            3 => 0.01,   // 3x
            4 => 0.005,  // 4x
            5 => 0.003,  // 5x
            6 => 0.002,
            7 => 0.001,
            8 => 0.0008,
        ];

        return ($baseAmplitudes[$harmonic] ?? 0.0005) * $multiplier;
    }
}
