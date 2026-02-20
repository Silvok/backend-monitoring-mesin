<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnalysisUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $machineId;
    public string $machineName;
    public ?string $location;
    public string $status;
    public ?float $rms;
    public ?float $peakAmp;
    public ?float $dominantFreq;
    public string $lastCheck;

    public function __construct(
        int $machineId,
        string $machineName,
        ?string $location,
        string $status,
        ?float $rms,
        ?float $peakAmp,
        ?float $dominantFreq,
        string $lastCheck
    ) {
        $this->machineId = $machineId;
        $this->machineName = $machineName;
        $this->location = $location;
        $this->status = $status;
        $this->rms = $rms;
        $this->peakAmp = $peakAmp;
        $this->dominantFreq = $dominantFreq;
        $this->lastCheck = $lastCheck;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("machine.{$this->machineId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'analysis.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'machine_id' => $this->machineId,
            'machine_name' => $this->machineName,
            'location' => $this->location,
            'status' => $this->status,
            'rms' => $this->rms,
            'peak_amp' => $this->peakAmp,
            'dominant_freq' => $this->dominantFreq,
            'last_check' => $this->lastCheck,
        ];
    }
}
