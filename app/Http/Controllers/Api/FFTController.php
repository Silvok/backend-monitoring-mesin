<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\AnalysisResult;
use App\Models\FFTResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FFTController extends Controller
{
    /**
     * Get latest FFT data for a specific machine
     */
    public function getLatestFFT(Request $request)
    {
        try {
            $machineId = $request->input('machine_id');

            $cacheKey = "fft_latest_{$machineId}";

            $fftData = Cache::remember($cacheKey, 30, function () use ($machineId) {
                $query = AnalysisResult::with(['machine', 'fftResult'])
                    ->whereHas('fftResult');

                if ($machineId) {
                    $query->where('machine_id', $machineId);
                }

                $latestAnalysis = $query->latest()->first();

                if (!$latestAnalysis || !$latestAnalysis->fftResult) {
                    return null;
                }

                $fft = $latestAnalysis->fftResult;

                // Get dominant frequencies (top 5 peaks)
                $dominantFreqs = $this->getDominantFrequencies(
                    $fft->frequencies,
                    $fft->amplitudes,
                    5
                );

                return [
                    'machine_id' => $latestAnalysis->machine_id,
                    'machine_name' => $latestAnalysis->machine->name ?? 'Unknown',
                    'analysis_id' => $latestAnalysis->id,
                    'frequencies' => $fft->frequencies,
                    'amplitudes' => $fft->amplitudes,
                    'dominant_frequencies' => $dominantFreqs,
                    'timestamp' => $latestAnalysis->created_at->format('Y-m-d H:i:s'),
                    'time_ago' => $latestAnalysis->created_at->diffForHumans(),
                ];
            });

            if (!$fftData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No FFT data available',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $fftData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching FFT data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FFT history for a machine
     */
    public function getFFTHistory(Request $request)
    {
        try {
            $machineId = $request->input('machine_id');
            $limit = $request->input('limit', 10);

            $query = AnalysisResult::with(['machine', 'fftResult'])
                ->whereHas('fftResult');

            if ($machineId) {
                $query->where('machine_id', $machineId);
            }

            $results = $query->latest()
                ->limit($limit)
                ->get()
                ->map(function ($analysis) {
                    $fft = $analysis->fftResult;
                    $dominantFreqs = $this->getDominantFrequencies(
                        $fft->frequencies,
                        $fft->amplitudes,
                        3
                    );

                    return [
                        'id' => $analysis->id,
                        'machine_id' => $analysis->machine_id,
                        'machine_name' => $analysis->machine->name ?? 'Unknown',
                        'dominant_freq' => $dominantFreqs[0]['frequency'] ?? 0,
                        'dominant_amp' => $dominantFreqs[0]['amplitude'] ?? 0,
                        'rms' => $analysis->rms,
                        'condition_status' => $analysis->condition_status,
                        'timestamp' => $analysis->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $analysis->created_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $results->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching FFT history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FFT spectrum for chart visualization
     */
    public function getFFTSpectrum(Request $request)
    {
        try {
            $machineId = $request->input('machine_id');
            $analysisId = $request->input('analysis_id');

            $query = AnalysisResult::with(['machine', 'fftResult'])
                ->whereHas('fftResult');

            if ($analysisId) {
                $query->where('id', $analysisId);
            } elseif ($machineId) {
                $query->where('machine_id', $machineId);
            }

            $analysis = $query->latest()->first();

            if (!$analysis || !$analysis->fftResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'No FFT spectrum data available'
                ]);
            }

            $fft = $analysis->fftResult;
            $frequencies = $fft->frequencies;
            $amplitudes = $fft->amplitudes;

            // Prepare chart data
            $chartData = [
                'labels' => array_map(function($f) {
                    return round($f, 1);
                }, $frequencies),
                'datasets' => [
                    [
                        'label' => 'Amplitudo (g)',
                        'data' => array_map(function($a) {
                            return round($a, 6);
                        }, $amplitudes),
                        'borderColor' => '#118B50',
                        'backgroundColor' => 'rgba(17, 139, 80, 0.2)',
                        'fill' => true,
                        'tension' => 0.1,
                        'pointRadius' => 0,
                        'borderWidth' => 1.5,
                    ]
                ]
            ];

            // Calculate spectrum statistics
            $maxAmplitude = max($amplitudes);
            $maxFreqIndex = array_search($maxAmplitude, $amplitudes);
            $dominantFreq = $frequencies[$maxFreqIndex] ?? 0;

            // Detect harmonics (multiples of dominant frequency)
            $harmonics = $this->detectHarmonics($frequencies, $amplitudes, $dominantFreq);

            // Analyze frequency bands
            $bandAnalysis = $this->analyzeBands($frequencies, $amplitudes);

            return response()->json([
                'success' => true,
                'data' => [
                    'machine_id' => $analysis->machine_id,
                    'machine_name' => $analysis->machine->name ?? 'Unknown',
                    'chart_data' => $chartData,
                    'statistics' => [
                        'dominant_frequency' => round($dominantFreq, 2),
                        'max_amplitude' => round($maxAmplitude, 6),
                        'total_points' => count($frequencies),
                        'frequency_range' => [
                            'min' => round(min($frequencies), 2),
                            'max' => round(max($frequencies), 2)
                        ]
                    ],
                    'harmonics' => $harmonics,
                    'band_analysis' => $bandAnalysis,
                    'timestamp' => $analysis->created_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching FFT spectrum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dominant frequencies from FFT data
     */
    private function getDominantFrequencies(array $frequencies, array $amplitudes, int $count = 5): array
    {
        $combined = [];
        foreach ($frequencies as $i => $freq) {
            $combined[] = [
                'frequency' => $freq,
                'amplitude' => $amplitudes[$i] ?? 0
            ];
        }

        // Sort by amplitude descending
        usort($combined, function($a, $b) {
            return $b['amplitude'] <=> $a['amplitude'];
        });

        // Return top N
        return array_slice($combined, 0, $count);
    }

    /**
     * Detect harmonics in FFT spectrum
     */
    private function detectHarmonics(array $frequencies, array $amplitudes, float $fundamentalFreq, float $tolerance = 0.1): array
    {
        if ($fundamentalFreq <= 0) return [];

        $harmonics = [];
        $maxHarmonic = 5;

        for ($n = 1; $n <= $maxHarmonic; $n++) {
            $targetFreq = $fundamentalFreq * $n;

            // Find closest frequency in spectrum
            $closestIndex = null;
            $closestDiff = PHP_FLOAT_MAX;

            foreach ($frequencies as $i => $freq) {
                $diff = abs($freq - $targetFreq);
                if ($diff < $closestDiff && $diff < ($targetFreq * $tolerance)) {
                    $closestDiff = $diff;
                    $closestIndex = $i;
                }
            }

            if ($closestIndex !== null) {
                $harmonics[] = [
                    'harmonic' => $n,
                    'expected_freq' => round($targetFreq, 2),
                    'actual_freq' => round($frequencies[$closestIndex], 2),
                    'amplitude' => round($amplitudes[$closestIndex], 6),
                ];
            }
        }

        return $harmonics;
    }

    /**
     * Analyze frequency bands (ISO 10816 based)
     */
    private function analyzeBands(array $frequencies, array $amplitudes): array
    {
        $bands = [
            'sub_synchronous' => ['min' => 0, 'max' => 10, 'label' => 'Sub-synchronous (0-10 Hz)', 'total' => 0, 'count' => 0],
            'low_freq' => ['min' => 10, 'max' => 100, 'label' => 'Low Frequency (10-100 Hz)', 'total' => 0, 'count' => 0],
            'mid_freq' => ['min' => 100, 'max' => 1000, 'label' => 'Mid Frequency (100-1000 Hz)', 'total' => 0, 'count' => 0],
            'high_freq' => ['min' => 1000, 'max' => 10000, 'label' => 'High Frequency (1-10 kHz)', 'total' => 0, 'count' => 0],
        ];

        foreach ($frequencies as $i => $freq) {
            $amp = $amplitudes[$i] ?? 0;

            foreach ($bands as $key => &$band) {
                if ($freq >= $band['min'] && $freq < $band['max']) {
                    $band['total'] += $amp;
                    $band['count']++;
                    break;
                }
            }
        }

        $result = [];
        foreach ($bands as $key => $band) {
            $result[] = [
                'band' => $key,
                'label' => $band['label'],
                'energy' => round($band['total'], 6),
                'points' => $band['count'],
                'avg_amplitude' => $band['count'] > 0 ? round($band['total'] / $band['count'], 6) : 0
            ];
        }

        return $result;
    }
}
