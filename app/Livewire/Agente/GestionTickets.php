<?php

namespace App\Livewire\Agente;

use App\Models\Ticket;
use App\Models\Cliente;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 

class GestionTickets extends Component
{
    use WithPagination;

    public $busqueda = '';
    protected $paginationTheme = 'bootstrap';

    // Propiedades para el formulario
    public $ticket_id;
    public $cliente_id_seleccionado;
    public $asunto;
    public $descripcion;
    public $estado = 'abierto';
    public $prioridad = 'media';
    public $agente_asignado_id_seleccionado;
    public $solucion_aplicada;
    public $display_fecha_resolucion = null; // Nueva propiedad para mostrar la fecha de resolución

    public $showTicketModal = false;

    protected function rules()
    {
        return [
            'cliente_id_seleccionado' => 'required|exists:clientes,id',
            'asunto' => 'required|string|min:5|max:255',
            'descripcion' => 'required|string|min:10',
            'estado' => 'required|string|in:abierto,en_proceso,pendiente_cliente,resuelto,cerrado',
            'prioridad' => 'required|string|in:baja,media,alta,urgente',
            'agente_asignado_id_seleccionado' => 'required|exists:users,id',
            'solucion_aplicada' => 'nullable|string|required_if:estado,resuelto|required_if:estado,cerrado|min:5',
        ];
    }

    protected $messages = [
        'cliente_id_seleccionado.required' => 'Debes seleccionar un cliente.',
        'cliente_id_seleccionado.exists' => 'El cliente seleccionado no es válido.',
        'asunto.required' => 'El asunto del ticket es obligatorio.',
        'asunto.min' => 'El asunto debe tener al menos 5 caracteres.',
        'descripcion.required' => 'La descripción del ticket es obligatoria.',
        'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
        'agente_asignado_id_seleccionado.required' => 'Debes asignar un agente al ticket.',
        'solucion_aplicada.required_if' => 'La solución es obligatoria si el ticket está resuelto o cerrado.',
        'solucion_aplicada.min' => 'La solución debe tener al menos 5 caracteres.',
    ];

    public function render()
    {
        $tickets = Ticket::with(['cliente', 'agenteCreador', 'agenteAsignado'])
                            ->where(function($query) {
                                $query->where('asunto', 'like', '%' . $this->busqueda . '%')
                                      ->orWhere('descripcion', 'like', '%' . $this->busqueda . '%')
                                      ->orWhereHas('cliente', function($subQuery) {
                                          $subQuery->where('nombre', 'like', '%' . $this->busqueda . '%')
                                                   ->orWhere('apellidos', 'like', '%' . $this->busqueda . '%');
                                      });
                            })
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        $clientesParaSelect = Cliente::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre', 'apellidos', 'telefono_principal']);
        $agentesParaSelect = User::whereHas('roles', function ($query) {
                                    $query->whereIn('name', ['agente', 'supervisor', 'administrador']); // Ajusta según tus roles
                                })->orderBy('name')->get(['id', 'name']);

        return view('livewire.agente.gestion-tickets', [
            'tickets' => $tickets,
            'clientesParaSelect' => $clientesParaSelect,
            'agentesParaSelect' => $agentesParaSelect,
        ]);
    }

    public function crearNuevoTicket()
    {
        $this->resetTicketInputFields();
        $this->showTicketModal = true;
    }

    private function resetTicketInputFields()
    {
        $this->ticket_id = null;
        $this->cliente_id_seleccionado = null;
        $this->asunto = '';
        $this->descripcion = '';
        $this->estado = 'abierto';
        $this->prioridad = 'media';
        $this->agente_asignado_id_seleccionado = Auth::id();
        $this->solucion_aplicada = '';
        $this->display_fecha_resolucion = null; // Reseteamos la fecha de resolución para mostrar
        $this->resetErrorBag(); // Limpia errores de validación previos
    }

    public function editarTicket(Ticket $ticket)
    {
        $this->resetErrorBag();
        $this->ticket_id = $ticket->id;
        $this->cliente_id_seleccionado = $ticket->cliente_id;
        $this->asunto = $ticket->asunto;
        $this->descripcion = $ticket->descripcion;
        $this->estado = $ticket->estado;
        $this->prioridad = $ticket->prioridad;
        $this->agente_asignado_id_seleccionado = $ticket->agente_asignado_id;
        $this->solucion_aplicada = $ticket->solucion_aplicada;
        $this->display_fecha_resolucion = $ticket->fecha_resolucion ? Carbon::parse($ticket->fecha_resolucion)->format('d/m/Y H:i:s') : null; // Cargamos para mostrar

        $this->showTicketModal = true;
    }

    public function guardarTicket()
    {
        $validatedData = $this->validate();

        $dataToSave = [
            'cliente_id' => $this->cliente_id_seleccionado, // Ya está en $validatedData por la property
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'agente_asignado_id' => $this->agente_asignado_id_seleccionado, // Ya está en $validatedData
            'solucion_aplicada' => (in_array($this->estado, ['resuelto', 'cerrado'])) ? $this->solucion_aplicada : null,
        ];
        
        if (!$this->ticket_id) { // Es un nuevo ticket
            $dataToSave['agente_creador_id'] = Auth::id();
        }

        // Lógica para fecha_resolucion
        if (in_array($dataToSave['estado'], ['resuelto', 'cerrado'])) {
            $ticketExistente = $this->ticket_id ? Ticket::find($this->ticket_id) : null;
            // Si es nuevo, o no tenía fecha, o el estado cambió a resuelto/cerrado y antes no lo era
            if (!$ticketExistente || !$ticketExistente->fecha_resolucion || !in_array($ticketExistente->estado, ['resuelto', 'cerrado'])) {
                $dataToSave['fecha_resolucion'] = Carbon::now();
            } else {
                // Mantener la fecha de resolución original si ya existía y el estado no ha cambiado significativamente
                $dataToSave['fecha_resolucion'] = $ticketExistente->fecha_resolucion;
            }
        } else {
            $dataToSave['fecha_resolucion'] = null; // Si no está resuelto/cerrado, la fecha es null
        }

        Ticket::updateOrCreate(['id' => $this->ticket_id], $dataToSave);

        session()->flash('message', $this->ticket_id ? 'Ticket actualizado correctamente.' : 'Ticket creado correctamente.');

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showTicketModal = false;
        $this->resetTicketInputFields();
    }
}