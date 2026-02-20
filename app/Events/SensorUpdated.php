<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $machineId;
    public ?float $ax;
    public ?float $ay;
    public ?float $az;
    public ?float $temperature;
    public string $timestamp;

    public function __construct(
        int $machineId,
        ?float $ax,
        ?float $ay,
        ?float $az,
        ?float $temperature,
        string $timestamp
    ) {
        $this->machineId = $machineId;
        $this->ax = $ax;
        $this->ay = $ay;
        $this->az = $az;
        $this->temperature = $temperature;
        $this->timestamp = $timestamp;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("machine.{$this->machineId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sensor.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'machine_id' => $this->machineId,
            'ax' => $this->ax,
            'ay' => $this->ay,
            'az' => $this->az,
            'temperature' => $this->temperature,
            'timestamp' => $this->timestamp,
        ];
    }
}
