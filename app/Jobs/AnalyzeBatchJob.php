<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ...tambahkan properti dan constructor jika diperlukan...

    public function handle(): void
    {
        try {
            // ...existing code (ambil batch, samples, analisis, simpan)...
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
