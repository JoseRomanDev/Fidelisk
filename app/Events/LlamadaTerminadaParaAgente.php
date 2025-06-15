<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LlamadaTerminadaParaAgente implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $llamadaId,
        public int $targetAgentId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('agente.' . $this->targetAgentId),
        ];
    }
}