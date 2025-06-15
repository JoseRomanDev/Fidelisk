<?php

namespace App\Livewire\Agente;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Llamada;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LlamadaActivaPanel extends Component
{
    public ?Llamada $llamadaActiva = null;
    public ?Cliente $clienteAsociado = null;

    // Variables para modales
    public bool $showCrearClienteModal = false;
    public bool $showVerClienteModal = false;

    // Campos para crear cliente
    public $nombre = '';
    public $apellidos = '';
    public $telefono_principal = '';
    public $email = '';
    public $direccion_completa = '';
    public $notas_agente = '';
    public $estado = 'activo';
    public $es_contacto_empresa = false;
    public $nombre_empresa_representada = '';
    public $puesto_contacto_empresa = '';

    // Variable para mostrar cliente en modal
    public ?Cliente $clienteParaVer = null;

    // Cambiamos método boot para asegurar la carga correcta
    protected function getListeners()
    {
        $agenteId = Auth::id();
        return [
            'llamada-actualizada' => 'handleLlamadaActualizada',
            "echo-private:agentes.{$agenteId},LlamadaTerminadaParaAgente" => 'handleLlamadaTerminada',
            "echo:llamadas,LlamadaActualizada" => 'handleLlamadaActualizada',
            "echo-private:agentes.{$agenteId},LlamadaActualizada" => 'handleLlamadaActualizada',
        ];
    }

    public function mount()
    {
        $agenteId = Auth::id();
        if ($agenteId) {
            // Buscar llamadas activas para este agente o disponibles en cola
            $llamadaActiva = Llamada::with('cliente')
                ->where(function($query) use ($agenteId) {
                    // Llamadas en curso del agente
                    $query->where(function($q) use ($agenteId) {
                        $q->where('estado', 'en_curso')
                          ->where('agente_id', $agenteId);
                    })
                    // O llamadas sonando disponibles para todos (cola)
                    ->orWhere(function($q) {
                        $q->where('estado', 'sonando')
                          ->whereNull('agente_id');
                    })
                    // O llamadas sonando asignadas a este agente específico
                    ->orWhere(function($q) use ($agenteId) {
                        $q->where('estado', 'sonando')
                          ->where('agente_id', $agenteId);
                    });
                })
                ->orderBy('hora_inicio', 'desc')
                ->first();

            if ($llamadaActiva) {
                $this->llamadaActiva = $llamadaActiva;
                $this->clienteAsociado = $llamadaActiva->cliente;
                Log::info("LlamadaActivaPanel: Montado con llamada activa para agente {$agenteId}: {$llamadaActiva->unique_id_asterisk} (estado: {$llamadaActiva->estado})");
            } else {
                Log::info("LlamadaActivaPanel: Montado para agente {$agenteId}, sin llamadas activas al inicio.");
            }
        } else {
            Log::warning("LlamadaActivaPanel: Componente montado sin usuario autenticado.");
        }
    }

    public function abrirModalCrearCliente()
    {
        $this->resetValidation();
        $this->reset([
            'nombre', 'apellidos', 'email', 'direccion_completa', 'notas_agente',
            'es_contacto_empresa', 'nombre_empresa_representada', 'puesto_contacto_empresa'
        ]);
        $this->telefono_principal = $this->llamadaActiva->caller_id_num ?? '';
        $this->estado = 'activo';
        $this->showCrearClienteModal = true;
    }

    public function cerrarModalCrearCliente()
    {
        $this->showCrearClienteModal = false;
    }

    public function guardarNuevoCliente()
    {
        $validated = $this->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'telefono_principal' => [
                'required',
                'string',
                Rule::unique('clientes', 'telefono_principal')->ignore($this->clienteAsociado->id ?? null, 'id'),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('clientes', 'email')->ignore($this->clienteAsociado->id ?? null, 'id'),
            ],
            'direccion_completa' => 'nullable|string',
            'notas_agente' => 'nullable|string',
            'estado' => 'required|string|in:activo,dado_de_baja',
            'es_contacto_empresa' => 'boolean',
            'nombre_empresa_representada' => 'nullable|string|max:255|required_if:es_contacto_empresa,true',
            'puesto_contacto_empresa' => 'nullable|string|max:255',
        ]);

        if (!$this->es_contacto_empresa) {
            $validated['nombre_empresa_representada'] = null;
            $validated['puesto_contacto_empresa'] = null;
        }

        $cliente = Cliente::create($validated);

        // Asociar cliente a la llamada activa
        if ($this->llamadaActiva) {
            $this->llamadaActiva->cliente_id = $cliente->id;
            $this->llamadaActiva->save();
            $this->dispatch('clienteCreadoDesdePanel', $cliente);
        }

        $this->clienteAsociado = $cliente;
        $this->cerrarModalCrearCliente();
        session()->flash('message', 'Cliente creado correctamente.');
    }

    public function abrirModalVerCliente()
    {
        $this->clienteParaVer = $this->clienteAsociado;
        $this->showVerClienteModal = true;
    }

    public function cerrarModalVerCliente()
    {
        $this->showVerClienteModal = false;
        $this->clienteParaVer = null;
    }

    #[On('llamada-actualizada')]
public function handleLlamadaActualizada($llamada = null, $agente_id = null): void
{
    $agenteActualId = Auth::id();
    if (!$agenteActualId) {
        Log::warning('LlamadaActivaPanel: Agente no autenticado. Ignorando evento.');
        return;
    }

    // Evento de terminación
    if (isset($llamada['esTerminacion']) && $llamada['esTerminacion'] === true) {
        $llamadaId = $llamada['llamadaId'] ?? null;
        if ($this->llamadaActiva && $this->llamadaActiva->unique_id_asterisk === $llamadaId) {
            $this->resetState();
            Log::info("LlamadaActivaPanel: Panel ocultado para agente {$agenteActualId} (no contestó)");
        }
        return;
    }

    // Normalización de datos
    if (is_array($llamada) && isset($llamada['llamada'])) {
        $data = $llamada;
    } else {
        $data = ['llamada' => $llamada, 'agente_id' => $agente_id];
    }

    $llamadaData = $data['llamada'] ?? null;
    if (!$llamadaData) {
        Log::warning('LlamadaActivaPanel: Datos de llamada no encontrados.');
        return;
    }

    $llamadaUniqueId = $llamadaData['unique_id_asterisk'] ?? null;
    if (!$llamadaUniqueId) {
        Log::warning('LlamadaActivaPanel: ID de llamada no encontrado.');
        return;
    }

    // Buscar llamada en BD
    $llamada = Llamada::with('cliente')->where('unique_id_asterisk', $llamadaUniqueId)->first();
    if (!$llamada) {
        Log::warning("LlamadaActivaPanel: Llamada {$llamadaUniqueId} no encontrada.");
        return;
    }

    // CLAVE: Evaluar claramente cuando debe mostrarse la llamada
    $debeMostrarLlamada = false;
    $esLlamadaRegresando = false;
    
    // Caso 1: Llamada en cola general (para todos)
    if ($llamada->estado === 'sonando' && is_null($llamada->agente_id)) {
        $debeMostrarLlamada = true;
        Log::debug("LlamadaActivaPanel: Llamada {$llamadaUniqueId} en cola");
    }
    
    // Caso 2: Llamada sonando para este agente
    elseif ($llamada->estado === 'sonando' && $llamada->agente_id === $agenteActualId) {
        $debeMostrarLlamada = true;
        
        if ($llamada->hora_atencion) {
            $esLlamadaRegresando = true;
            Log::debug("LlamadaActivaPanel: Llamada {$llamadaUniqueId} REGRESANDO al agente {$agenteActualId}");
        } else {
            Log::debug("LlamadaActivaPanel: Llamada {$llamadaUniqueId} nueva para agente {$agenteActualId}");
        }
    }
    
    // Caso 3: Llamada en curso para este agente
    elseif ($llamada->estado === 'en_curso' && $llamada->agente_id === $agenteActualId) {
        $debeMostrarLlamada = true;
        Log::debug("LlamadaActivaPanel: Llamada {$llamadaUniqueId} en curso para agente {$agenteActualId}");
    }

    // APLICAR DECISIÓN
    if ($debeMostrarLlamada) {
        $this->llamadaActiva = $llamada;
        $this->clienteAsociado = $llamada->cliente;
        
        // CLAVE: Usar dispatch para notificar y forzar actualización visual
        $this->dispatch('$refresh');
        $this->dispatch('llamada-panel-updated', [
            'llamadaId' => $llamada->id,
            'estado' => $llamada->estado,
            'regresa' => $esLlamadaRegresando,
            'timestamp' => now()->timestamp
        ]);
        
        Log::info("LlamadaActivaPanel: MOSTRANDO panel para agente {$agenteActualId}, llamada {$llamadaUniqueId}");
    } 
    elseif ($this->llamadaActiva && $this->llamadaActiva->unique_id_asterisk === $llamadaUniqueId) {
        $this->resetState();
        Log::info("LlamadaActivaPanel: OCULTANDO panel para agente {$agenteActualId}, llamada {$llamadaUniqueId}");
    }
}

    /**
     * Maneja eventos de llamada terminada (cuando el agente no contesta)
     */
    public function handleLlamadaTerminada($data): void
    {
        $agenteActualId = Auth::id();
        $llamadaId = $data['llamadaId'] ?? null;
        
        Log::info("LlamadaActivaPanel para agente {$agenteActualId}: Recibido evento para ocultar panel para llamada {$llamadaId}");
        
        // Si tenemos una llamada activa y coincide con la del evento, resetear para ocultarla
        if ($this->llamadaActiva && $this->llamadaActiva->unique_id_asterisk === $llamadaId) {
            $this->resetState();
            Log::info("LlamadaActivaPanel: Panel ocultado para agente {$agenteActualId} (no contestó)");
        }
    }

    /**
     * Método para forzar actualización manual (para debugging)
     */
    public function forceRefresh(): void
    {
        $agenteId = Auth::id();
        if ($agenteId) {
            // Buscar llamadas activas para este agente o disponibles en cola
            $llamadaActiva = Llamada::with('cliente')
                ->where(function($query) use ($agenteId) {
                    // Llamadas en curso del agente
                    $query->where(function($q) use ($agenteId) {
                        $q->where('estado', 'en_curso')
                          ->where('agente_id', $agenteId);
                    })
                    // O llamadas sonando disponibles para todos (cola)
                    ->orWhere(function($q) {
                        $q->where('estado', 'sonando')
                          ->whereNull('agente_id');
                    })
                    // O llamadas sonando asignadas a este agente específico
                    ->orWhere(function($q) use ($agenteId) {
                        $q->where('estado', 'sonando')
                          ->where('agente_id', $agenteId);
                    });
                })
                ->orderBy('hora_inicio', 'desc')
                ->first();

            if ($llamadaActiva) {
                $this->llamadaActiva = $llamadaActiva;
                $this->clienteAsociado = $llamadaActiva->cliente;
                Log::info("LlamadaActivaPanel: Actualización manual - encontrada llamada activa: {$llamadaActiva->unique_id_asterisk}");
            } else {
                $this->resetState();
                Log::info("LlamadaActivaPanel: Actualización manual - no hay llamadas activas");
            }
        }
    }

    public function resetState(): void
    {
        $this->reset([
            'llamadaActiva',
            'clienteAsociado',
            'showCrearClienteModal',
            'showVerClienteModal',
            'clienteParaVer',
            'nombre',
            'apellidos',
            'telefono_principal',
            'email',
            'direccion_completa',
            'notas_agente',
            'es_contacto_empresa',
            'nombre_empresa_representada',
            'puesto_contacto_empresa'
        ]);
        $this->resetValidation();
        Log::debug("LlamadaActivaPanel: Estado reseteado - componente oculto");
    }

    public function render(): View
    {
        return view('livewire.agente.llamada-activa-panel', [
            'agenteId' => Auth::id()
        ]);
    }
}