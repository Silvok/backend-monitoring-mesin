<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Machine;

class AnalyzeBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function handle(): void
    {
        try {
            if (!Schema::hasTable('raw_samples') || !Schema::hasTable('analysis_results')) {
                \Log::warning('AnalyzeBatchJob skipped: missing tables', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $rawSampleColumns = array_flip(Schema::getColumnListing('raw_samples'));
            $analysisColumns = array_flip(Schema::getColumnListing('analysis_results'));

            $query = DB::table('raw_samples');
            if (array_key_exists('raw_batch_id', $rawSampleColumns)) {
                $query->where('raw_batch_id', $this->batchId);
            } elseif (array_key_exists('batch_id', $rawSampleColumns)) {
                $query->where('batch_id', $this->batchId);
            } else {
                \Log::warning('AnalyzeBatchJob skipped: no batch id column', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $selectCols = [];
            foreach (['ax_g', 'ay_g', 'az_g', 'value', 'machine_id'] as $col) {
                if (array_key_exists($col, $rawSampleColumns)) {
                    $selectCols[] = $col;
                }
            }
            if (empty($selectCols)) {
                \Log::warning('AnalyzeBatchJob skipped: no sample columns', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $samples = $query->select($selectCols)->get();
            if ($samples->isEmpty()) {
                \Log::warning('AnalyzeBatchJob skipped: empty samples', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $values = [];
            $hasAxis = array_key_exists('ax_g', $rawSampleColumns)
                || array_key_exists('ay_g', $rawSampleColumns)
                || array_key_exists('az_g', $rawSampleColumns);

            foreach ($samples as $row) {
                if ($hasAxis) {
                    $ax = isset($row->ax_g) ? (float) $row->ax_g : 0.0;
                    $ay = isset($row->ay_g) ? (float) $row->ay_g : 0.0;
                    $az = isset($row->az_g) ? (float) $row->az_g : 0.0;
                    $values[] = sqrt(($ax * $ax) + ($ay * $ay) + ($az * $az));
                } elseif (isset($row->value)) {
                    $values[] = (float) $row->value;
                }
            }

            if (empty($values)) {
                \Log::warning('AnalyzeBatchJob skipped: no numeric values', [
                    'batch_id' => $this->batchId ?? null,
                ]);
                return;
            }

            $count = count($values);
            $sum = array_sum($values);
            $mean = $sum / $count;
            $squareSum = 0.0;
            $peak = 0.0;
            foreach ($values as $v) {
                $squareSum += ($v * $v);
                $abs = abs($v);
                if ($abs > $peak) {
                    $peak = $abs;
                }
            }
            $rms = sqrt($squareSum / $count);
            $variance = 0.0;
            foreach ($values as $v) {
                $variance += pow($v - $mean, 2);
            }
            $std = sqrt($variance / $count);

            $machineId = $samples->first()->machine_id ?? null;
            $warningThreshold = 0.7;
            $criticalThreshold = 1.8;
            if ($machineId) {
                $machine = Machine::find($machineId);
                if ($machine) {
                    $warningThreshold = (float) ($machine->threshold_warning ?? $warningThreshold);
                    $criticalThreshold = (float) ($machine->threshold_critical ?? $criticalThreshold);
                }
            }

            $conditionStatus = 'NORMAL';
            if ($rms >= $criticalThreshold) {
                $conditionStatus = 'CRITICAL';
            } elseif ($rms >= $warningThreshold) {
                $conditionStatus = 'WARNING';
            }

            $analysisData = [
                'machine_id' => $machineId,
                'batch_id' => $this->batchId,
                'raw_batch_id' => $this->batchId,
                'rms' => $rms,
                'peak_amp' => $peak,
                'mean' => $mean,
                'std' => $std,
                'status' => 'done',
                'condition_status' => $conditionStatus,
                'name' => 'Analysis ' . now()->format('Ymd_His'),
                'result' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $analysisData = array_intersect_key($analysisData, $analysisColumns);

            if (!empty($analysisData)) {
                DB::table('analysis_results')->insert($analysisData);
            }
        } catch (\Throwable $e) {
            \Log::error('AnalyzeBatchJob FAILED', [
                'batch_id' => $this->batchId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    // ...existing code...
}
