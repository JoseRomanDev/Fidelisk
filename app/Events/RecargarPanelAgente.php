<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecargarPanelAgente implements ShouldBroadcastNow // Cambiado a ShouldBroadcastNow para que se transmita inmediatamente
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $agenteId;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(int $agenteId, string $message = 'Forzando recarga de panel')
    {
        $this->agenteId = $agenteId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // El canal privado para el agente específico
        return [
            new PrivateChannel('agentes.' . $this->agenteId),
        ];
    }

    /**
     * El nombre del evento tal como se transmitirá en el frontend.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'RecargarPanelAgente'; // Este es el nombre que usas en Livewire o JavaScript
    }

    /**
     * Los datos a transmitir con el evento.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'agenteId' => $this->agenteId,
            'message' => $this->message,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}