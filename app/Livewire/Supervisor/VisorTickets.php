<?php

namespace App\Livewire\Supervisor;

use App\Models\Ticket;
use App\Models\User; // Para filtrar por agentes
use App\Models\Cliente; // Para filtrar por clientes
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class VisorTickets extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // Usaremos la paginación de Tailwind

    // Propiedades para búsqueda y filtros
    public $busquedaGeneral = '';
    public $filtroAgenteId = ''; // ID del agente asignado o creador
    public $filtroClienteId = ''; // ID del cliente
    public $filtroEstado = ''; // abierto, en_proceso, resuelto, etc.
    public $filtroPrioridad = ''; // baja, media, alta, urgente
    public $fechaDesde = '';
    public $fechaHasta = '';

    // Para los dropdowns de los filtros
    public $agentesParaFiltrar = [];
    public $clientesParaFiltrar = [];
    public $estadosPosibles = ['abierto', 'en_proceso', 'pendiente_cliente', 'resuelto', 'cerrado']; // Podrían venir de config o DB
    public $prioridadesPosibles = ['baja', 'media', 'alta', 'urgente']; // Podrían venir de config o DB

    // Para la vista de detalles del ticket
    public $selectedTicketDetails = null;
    public $showViewTicketModal = false;

    // Para la reasignación de tickets
    public ?Ticket $ticketParaReasignar = null; // Usamos ?Ticket para permitir null y tipado
    public $nuevoAgenteIdParaReasignar = '';
    public $showReasignarModal = false;

    public function mount()
    {
        // Cargar datos iniciales para los filtros si es necesario
        $this->agentesParaFiltrar = User::whereHas('roles', function ($query) {
                                        // Asumimos que los agentes tienen el rol 'agente'
                                        // O podrías tener una forma más específica de identificar usuarios que son agentes
                                        $query->whereIn('name', ['agente', 'supervisor']); // Supervisores también pueden tener tickets asignados
                                    })
                                    ->orderBy('name')
                                    ->get(['id', 'name']);

        $this->clientesParaFiltrar = Cliente::orderBy('nombre')->get(['id', 'nombre', 'apellidos']);
    }

    public function updated($propertyName)
    {
        // Si cambia algún filtro o búsqueda, resetea la paginación a la primera página
        if (in_array($propertyName, ['busquedaGeneral', 'filtroAgenteId', 'filtroClienteId', 'filtroEstado', 'filtroPrioridad', 'fechaDesde', 'fechaHasta'])) {
            $this->resetPage();
        }
    }
    
    public function limpiarFiltros()
    {
        $this->reset(['busquedaGeneral', 'filtroAgenteId', 'filtroClienteId', 'filtroEstado', 'filtroPrioridad', 'fechaDesde', 'fechaHasta']);
        $this->resetPage();
    }

    public function verDetallesTicket($ticketId)
    {
        $this->selectedTicketDetails = Ticket::with([
            'cliente', 
            'agenteCreador', 
            'agenteAsignado'
        ])->find($ticketId);

        if ($this->selectedTicketDetails) {
            $this->showViewTicketModal = true;
        }
    }
     public function closeViewTicketModal()
    {
        $this->showViewTicketModal = false;
        $this->selectedTicketDetails = null; // Limpiar el ticket seleccionado
    }
    public function abrirModalReasignar($ticketId)
    {
        $this->ticketParaReasignar = Ticket::find($ticketId);
        if ($this->ticketParaReasignar) {
            // Pre-seleccionar el agente actual si existe, o dejarlo vacío
            $this->nuevoAgenteIdParaReasignar = $this->ticketParaReasignar->agente_asignado_id ?? '';
            $this->showReasignarModal = true;
        } else {
            session()->flash('error', 'No se pudo encontrar el ticket para reasignar.');
        }
    }

    public function reasignarTicketSeleccionado()
    {
        $this->validate([
            'nuevoAgenteIdParaReasignar' => 'required|exists:users,id',
            // 'ticketParaReasignar.id' => 'required|exists:tickets,id' // Ya tenemos el objeto ticket
        ]);

        if ($this->ticketParaReasignar) {
            $this->ticketParaReasignar->agente_asignado_id = $this->nuevoAgenteIdParaReasignar;
            $this->ticketParaReasignar->save();

            session()->flash('message', "Ticket #{$this->ticketParaReasignar->id} reasignado correctamente.");
            $this->closeReasignarModal();
            // La tabla se refrescará automáticamente en el siguiente render de Livewire
        } else {
            session()->flash('error', 'Error al reasignar el ticket.');
        }
    }

    public function closeReasignarModal()
    {
        $this->showReasignarModal = false;
        $this->ticketParaReasignar = null;
        $this->nuevoAgenteIdParaReasignar = '';
    }
    
    public function render()
    {
        $query = Ticket::with(['cliente', 'agenteCreador', 'agenteAsignado'])
                        ->orderBy('created_at', 'desc'); // Ordenar por más recientes primero

        // Aplicar búsqueda general
        if (!empty($this->busquedaGeneral)) {
            $query->where(function ($q) {
                $q->where('asunto', 'like', '%' . $this->busquedaGeneral . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->busquedaGeneral . '%')
                  ->orWhereHas('cliente', function ($subQ) {
                      $subQ->where('nombre', 'like', '%' . $this->busquedaGeneral . '%')
                           ->orWhere('apellidos', 'like', '%' . $this->busquedaGeneral . '%');
                  })
                  ->orWhereHas('agenteAsignado', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->busquedaGeneral . '%');
                  })
                  ->orWhereHas('agenteCreador', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->busquedaGeneral . '%');
                  });
            });
        }

        // Aplicar filtro por Agente (asignado o creador)
        if (!empty($this->filtroAgenteId)) {
            $query->where(function($q) {
                $q->where('agente_asignado_id', $this->filtroAgenteId)
                  ->orWhere('agente_creador_id', $this->filtroAgenteId);
            });
        }

        // Aplicar filtro por Cliente
        if (!empty($this->filtroClienteId)) {
            $query->where('cliente_id', $this->filtroClienteId);
        }

        // Aplicar filtro por Estado
        if (!empty($this->filtroEstado)) {
            $query->where('estado', $this->filtroEstado);
        }

        // Aplicar filtro por Prioridad
        if (!empty($this->filtroPrioridad)) {
            $query->where('prioridad', $this->filtroPrioridad);
        }
        
        // Aplicar filtro por Rango de Fechas (fecha de creación del ticket)
        if (!empty($this->fechaDesde)) {
            $query->whereDate('created_at', '>=', $this->fechaDesde);
        }
        if (!empty($this->fechaHasta)) {
            $query->whereDate('created_at', '<=', $this->fechaHasta);
        }

        $tickets = $query->paginate(15); // Mostrar 15 tickets por página

        return view('livewire.supervisor.visor-tickets', [
            'tickets' => $tickets,
        ]);
    }
}