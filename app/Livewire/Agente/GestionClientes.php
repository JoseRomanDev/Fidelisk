<?php

namespace App\Livewire\Agente;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; 

class GestionClientes extends Component
{
    use WithPagination;

    public $busqueda = '';
    protected $paginationTheme = 'bootstrap';

    // Propiedades para el formulario del cliente
    public $cliente_id; // Para identificar si estamos editando o creando
    public $nombre;
    public $apellidos;
    public $telefono_principal;
    public $email;
    public $direccion_completa;
    public $notas_agente;
    public $estado = 'activo'; // Valor por defecto
    public $es_contacto_empresa = false; // Valor por defecto
    public $nombre_empresa_representada;
    public $puesto_contacto_empresa;
    public $fecha_baja; // Para mostrar, no editable directamente

    public $showModal = false;

    public $clienteIdParaAccion;
    public $showConfirmacionBajaModal = false;
    public $showConfirmacionReactivarModal = false;

    protected function rules() // Definir rules como método para dinamismo
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'telefono_principal' => [
                'required',
                'string',
                Rule::unique('clientes', 'telefono_principal')->ignore($this->cliente_id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('clientes', 'email')->ignore($this->cliente_id),
            ],
            'direccion_completa' => 'nullable|string',
            'notas_agente' => 'nullable|string',
            'estado' => 'required|string|in:activo,dado_de_baja',
            'es_contacto_empresa' => 'boolean',
            'nombre_empresa_representada' => 'nullable|string|max:255|required_if:es_contacto_empresa,true',
            'puesto_contacto_empresa' => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El campo nombre es obligatorio.',
        'telefono_principal.required' => 'El teléfono principal es obligatorio.',
        'telefono_principal.unique' => 'Este teléfono ya está registrado para otro cliente.',
        'email.email' => 'El formato del email no es válido.',
        'email.unique' => 'Este email ya está registrado para otro cliente.',
        'nombre_empresa_representada.required_if' => 'El nombre de la empresa es obligatorio si es un contacto de empresa.',
    ];

    #[On('clienteCreadoDesdePanel')]
    #[On('clienteActualizadoDesdePanel')] // Escuchar también si se actualiza desde el panel de llamada
    public function refreshClientList()
    {
        Log::info('GestionClientes: Evento para refrescar lista de clientes recibido.');
        $this->resetPage(); // Resetea la paginación para mostrar el nuevo cliente
        // El componente se re-renderizará automáticamente
    }
    
    #[On('mostrar-cliente-gestion')]
    public function mostrarClienteEnGestion($clienteId)
    {
        $cliente = Cliente::find($clienteId);
        if ($cliente) {
            $this->editarCliente($cliente); // Reutiliza el método editar para abrir el modal con los datos
        }
    }

    public function render()
    {
        $query = Cliente::query();

        if (!empty($this->busqueda)) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('telefono_principal', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('email', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('nombre_empresa_representada', 'like', '%' . $this->busqueda . '%');
            });
        }
        
        $clientes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.agente.gestion-clientes', [
            'clientes' => $clientes,
        ]);
    }

    public function crearNuevoCliente()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    private function resetInputFields()
    {
        $this->cliente_id = null;
        $this->nombre = '';
        $this->apellidos = '';
        $this->telefono_principal = '';
        $this->email = '';
        $this->direccion_completa = '';
        $this->notas_agente = '';
        $this->estado = 'activo';
        $this->es_contacto_empresa = false;
        $this->nombre_empresa_representada = '';
        $this->puesto_contacto_empresa = '';
        $this->fecha_baja = null;
        $this->clienteIdParaAccion = null; 
        $this->resetValidation();
    }

    public function editarCliente(Cliente $cliente)
    {
        $this->resetInputFields();
        $this->cliente_id = $cliente->id;
        $this->nombre = $cliente->nombre;
        $this->apellidos = $cliente->apellidos;
        $this->telefono_principal = $cliente->telefono_principal;
        $this->email = $cliente->email;
        $this->direccion_completa = $cliente->direccion_completa;
        $this->notas_agente = $cliente->notas_agente;
        $this->estado = $cliente->estado;
        $this->es_contacto_empresa = (bool)$cliente->es_contacto_empresa;
        $this->nombre_empresa_representada = $cliente->nombre_empresa_representada;
        $this->puesto_contacto_empresa = $cliente->puesto_contacto_empresa;
        $this->fecha_baja = $cliente->fecha_baja ? Carbon::parse($cliente->fecha_baja)->format('d/m/Y H:i') : null;
        $this->showModal = true;
    }

    public function guardarCliente()
    {
        $validatedData = $this->validate(); // Las rules se obtienen del método rules()

        if (!$this->es_contacto_empresa) {
            $validatedData['nombre_empresa_representada'] = null;
            $validatedData['puesto_contacto_empresa'] = null;
        }

        // Manejar fecha_baja según el estado
        if ($validatedData['estado'] === 'activo') {
            $validatedData['fecha_baja'] = null;
        } elseif ($validatedData['estado'] === 'dado_de_baja') {
            // Si se está creando nuevo como 'dado_de_baja' o si se está actualizando a 'dado_de_baja' y no tenía fecha
            if (!$this->cliente_id || (Cliente::find($this->cliente_id) && !Cliente::find($this->cliente_id)->fecha_baja) ) {
                 $validatedData['fecha_baja'] = Carbon::now();
            }
        }

        Cliente::updateOrCreate(['id' => $this->cliente_id], $validatedData);
        session()->flash('message', $this->cliente_id ? 'Cliente actualizado correctamente.' : 'Cliente creado correctamente.');
        $this->closeModal();
        $this->refreshClientList(); // Refrescar la lista después de guardar
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function confirmarDarDeBaja($clienteId)
    {
        $this->clienteIdParaAccion = $clienteId;
        $this->showConfirmacionBajaModal = true;
    }

    public function darDeBajaCliente()
    {
        $cliente = Cliente::find($this->clienteIdParaAccion);
        if ($cliente) {
            $cliente->estado = 'dado_de_baja';
            $cliente->fecha_baja = Carbon::now();
            $cliente->save();
            session()->flash('message', 'Cliente dado de baja correctamente.');
        } else {
            session()->flash('error', 'No se pudo encontrar el cliente.');
        }
        $this->showConfirmacionBajaModal = false;
        $this->resetInputFields(); 
        $this->refreshClientList();
    }

    public function confirmarReactivar($clienteId)
    {
        $this->clienteIdParaAccion = $clienteId;
        $this->showConfirmacionReactivarModal = true;
    }

    public function reactivarCliente()
    {
        $cliente = Cliente::find($this->clienteIdParaAccion);
        if ($cliente) {
            $cliente->estado = 'activo';
            $cliente->fecha_baja = null; 
            $cliente->save();
            session()->flash('message', 'Cliente reactivado correctamente.');
        } else {
            session()->flash('error', 'No se pudo encontrar el cliente.');
        }
        $this->showConfirmacionReactivarModal = false;
        $this->resetInputFields();
        $this->refreshClientList();
    }
    
    // Este método ya estaba, lo mantengo por si se usa desde otro lado.
    // El panel de llamada activa ahora usa 'mostrar-cliente-gestion'
    #[On('crearClienteDesdeLlamada')] 
    public function crearClienteDesdeLlamada($telefono)
    {
        $this->resetInputFields();
        $this->telefono_principal = $telefono;
        $this->showModal = true;
    }
    
    // Este método ya estaba, lo mantengo por si se usa desde otro lado.
    // El panel de llamada activa ahora usa 'mostrar-cliente-gestion'
    #[On('verClienteDesdeLlamada')]
    public function verClienteDesdeLlamada($clienteId)
    {
        $cliente = Cliente::find($clienteId);
        if ($cliente) {
            $this->editarCliente($cliente);
        } else {
            session()->flash('error', 'No se pudo encontrar el cliente para ver/editar.');
        }
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }
}