<?php

namespace App\Livewire\Supervisor;

use Livewire\Component;
use App\Models\User;
use App\Models\Ticket; 
use Carbon\Carbon;      // Para trabajar con fechas

class EstadisticasAgentes extends Component
{
    public $agentes = []; // Para la lista de agentes y sus datos
    public ?User $selectedAgent = null; // Para el agente seleccionado en el modal
    public $dailyTickets = [];      // Para los tickets del día del agente seleccionado
    public $showDailyTicketsModal = false;

    public function mount()
    {
        $this->loadAgentesData();
    }

    public function loadAgentesData()
    {
        // Obtener todos los usuarios con rol 'agente'
        $this->agentes = User::whereHas('roles', function ($query) {
            $query->where('name', 'agente');
        })->get()->map(function ($agente) {
            // 1. Determinar el estado online
            // Consideramos online si is_online es true Y fue visto recientemente (ej. últimos 5 minutos)
            // Puedes ajustar el tiempo (5 minutos) según tus necesidades.
            $isOnlineBasedOnDb = $agente->is_online && $agente->last_seen_at && $agente->last_seen_at->gt(Carbon::now()->subMinutes(5));
            
            // Para una simplificación inicial, si prefieres no usar last_seen_at todavía:
             $isOnlineBasedOnDb = $agente->is_online;

            $agente->computed_online_status = $isOnlineBasedOnDb;

            $agente->tickets_resueltos_hoy_count = Ticket::where('agente_asignado_id', $agente->id) // O el campo que uses para quien resolvió
                                                       ->whereIn('estado', ['resuelto', 'cerrado']) // Ajusta los estados si es necesario
                                                       ->whereDate('fecha_resolucion', Carbon::today()) // O updated_at si usas esa
                                                       ->count();
            return $agente;
        });
    }

    public function openDailyTicketsModal($agentId)
    {
        $this->selectedAgent = User::find($agentId);
        if ($this->selectedAgent) {
            $this->dailyTickets = Ticket::where('agente_asignado_id', $this->selectedAgent->id) // O el campo que uses
                                        ->whereIn('estado', ['resuelto', 'cerrado'])
                                        ->whereDate('fecha_resolucion', Carbon::today()) // O updated_at
                                        ->orderBy('fecha_resolucion', 'desc') // Opcional: ordenar
                                        ->get();
            $this->showDailyTicketsModal = true;
        }
    }

    public function closeDailyTicketsModal()
    {
        $this->showDailyTicketsModal = false;
        $this->selectedAgent = null;
        $this->dailyTickets = [];
    }

    public function render()
    {
        // Si quieres que los datos se refresquen automáticamente, puedes llamar a loadAgentesData() aquí,
        // especialmente si usas wire:poll. O, si mount() es suficiente y wire:poll hace el re-render,
        // la vista ya tendrá los datos actualizados.
        // Para un polling más explícito refrescando datos:
         $this->loadAgentesData(); // si quieres forzar la recarga con cada render de poll

        return view('livewire.supervisor.estadisticas-agentes', [
            'agentesData' => $this->agentes,
        ]);
    }
}