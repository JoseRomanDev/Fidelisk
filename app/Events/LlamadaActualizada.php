<?php

namespace App\Events;

use App\Models\Llamada;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LlamadaActualizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $llamada;
    public $agenteId;

    public function __construct(Llamada $llamada, $agenteId = null)
    {
        $this->llamada = $llamada;
        $this->agenteId = $agenteId;
    }

    public function broadcastWith()
    {
        return [
            'llamada' => $this->llamada->toArray(),
            'agente_id' => $this->agenteId,
        ];
    }

    public function broadcastOn()
    {
        $channels = [];

        // Si hay un agente específico, transmitir a su canal privado
        if ($this->agenteId && is_numeric($this->agenteId)) {
            $channels[] = new PrivateChannel('agentes.' . $this->agenteId);
            Log::debug("LlamadaActualizada: Transmitiendo a PrivateChannel 'agentes.{$this->agenteId}' para llamada {$this->llamada->unique_id_asterisk}");
        }

        // Siempre transmitir también a un canal general para colas
        $channels[] = new Channel('llamadas');
        Log::debug("LlamadaActualizada: Transmitiendo también a Channel 'llamadas' para llamada {$this->llamada->unique_id_asterisk}");

        return $channels;
    }
}