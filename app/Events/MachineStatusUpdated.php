<?php

namespace App\Events;

use App\Models\Machine;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MachineStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $machine;
    public $status;
    public $prediction;

    /**
     * Create a new event instance.
     */
    public function __construct(Machine $machine, $status, $prediction = null)
    {
        $this->machine = $machine;
        $this->status = $status;
        $this->prediction = $prediction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('machines'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'machine.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'machine_id' => $this->machine->id,
            'machine_name' => $this->machine->name,
            'location' => $this->machine->location,
            'status' => $this->status,
            'prediction' => $this->prediction,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
