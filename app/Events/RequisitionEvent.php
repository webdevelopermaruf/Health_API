<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequisitionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public array $payload;
    public function __construct(public array $data) {
        $this->payload = $data;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('requisition-channel'); // PUBLIC channel
    }

    public function broadcastAs(): string
    {
        return 'requisition-channel';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
