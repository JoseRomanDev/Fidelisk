<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PAMI\Client\Impl\ClientImpl as PamiClient;
use PAMI\Message\Event\NewchannelEvent;
use PAMI\Message\Event\BridgeEnterEvent;
use PAMI\Message\Event\HangupEvent;
use PAMI\Message\Event\AgentCalledEvent;
use PAMI\Message\Event\AgentRingNoAnswerEvent;
use App\Models\Llamada;
use App\Models\Cliente;
use App\Models\User;
use App\Events\LlamadaActualizada;
use App\Events\LlamadaTerminadaParaAgente;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AsteriskListen extends Command
{
    protected $signature = 'asterisk:listen';
    protected $description = 'Escucha eventos de Asterisk AMI y procesa llamadas';

    public function handle()
    {
        $options = [
            'host' => env('ASTERISK_HOST'),
            'port' => env('ASTERISK_PORT'),
            'username' => env('ASTERISK_USERNAME'),
            'secret' => env('ASTERISK_SECRET'),
            'connect_timeout' => 10000,
            'read_timeout' => 10000,
        ];

        while (true) {
            try {
                $pamiClient = new PamiClient($options);
                $this->info("Conectando a AMI en " . $options['host'] . ":" . $options['port'] . "...");
                $pamiClient->open();
                $this->info("¡Conexión exitosa! Escuchando eventos...");

                $pamiClient->registerEventListener(function ($event) {
                    $command = $this;
                    Log::debug('Asterisk AMI Evento recibido', ['event_name' => $event->getName(), 'keys' => $event->getKeys()]);

                    // --- Evento: Nueva llamada entrante ---
                    if ($event instanceof NewchannelEvent && $event->getContext() === 'from-pstn') {
                        $uniqueId = $event->getUniqueId();
                        $callerNum = $event->getCallerIDNum();
                        $callerName = $event->getCallerIDName();
                        $numeroDestino = $event->getKey('Exten');

                        if ($uniqueId && $callerNum && $numeroDestino) {
                            $command->info("[{$uniqueId}] Nueva llamada entrante de: {$callerNum} -> {$numeroDestino}");

                            // CLAVE: Usar updateOrCreate para manejar llamadas que regresan
                            $llamada = Llamada::updateOrCreate(
                                ['unique_id_asterisk' => $uniqueId],
                                [
                                    'caller_id_num'  => $callerNum,
                                    'caller_id_name' => $callerName ?: 'Desconocido',
                                    'numero_destino' => $numeroDestino,
                                    'estado'         => 'sonando',
                                    'hora_inicio'    => Carbon::now(),
                                    'agente_id'      => null, // IMPORTANTE: Resetear agente_id cuando regresa
                                    'extension_sip'  => null, // IMPORTANTE: Resetear extension_sip cuando regresa
                                    // NOTA: NO reseteamos hora_atencion para detectar llamadas que regresan
                                    'hora_fin'       => null, // IMPORTANTE: Resetear hora_fin cuando regresa
                                    'causa_fin'      => null, // IMPORTANTE: Resetear causa_fin cuando regresa
                                ]
                            );

                            $cliente = Cliente::where('telefono_principal', $callerNum)->first();
                            if ($cliente) {
                                $llamada->cliente_id = $cliente->id;
                                $llamada->save();
                                $command->info("[{$uniqueId}] Llamada asociada al cliente existente: {$cliente->nombre}");
                            }

                            // IMPORTANTE: Disparar evento para TODAS las llamadas de cola
                            event(new LlamadaActualizada($llamada->fresh(), null));
                            Log::info("[{$uniqueId}] LlamadaActualizada evento disparado para llamada en cola (puede ser nueva o que regresa)");
                            
                            Log::info("[{$uniqueId}] NewchannelEvent: Llamada creada/actualizada. Esperando asignación a agente.");
                        } else {
                            Log::warning("NewchannelEvent: Datos insuficientes para procesar la llamada. UniqueId: {$uniqueId}, CallerNum: {$callerNum}, Destino: {$numeroDestino}");
                        }
                    }

                    // --- Evento: Llamada atendida por un agente ---
                    if ($event instanceof BridgeEnterEvent) {
                        $linkedId = $event->getKey('Linkedid');
                        $channel = $event->getChannel();
                        $extension = null;

                        if (preg_match('/^(?:PJSIP|SIP)\/(\d+)-/', $channel, $matches)) {
                            $extension = $matches[1];
                        }
                        Log::debug("BridgeEnterEvent: LinkedId: {$linkedId}, Channel: {$channel}, Extracted Extension: {$extension}");

                        if ($extension && $linkedId) {
                            $llamada = Llamada::where('unique_id_asterisk', $linkedId)
                                ->whereNotIn('estado', ['finalizada', 'perdida'])
                                ->first();

                            if ($llamada) {
                                $agente = User::where('extension_sip', $extension)->first();
                                if ($agente) {
                                    $llamada->update([
                                        'estado'        => 'en_curso',
                                        'agente_id'     => $agente->id,
                                        'extension_sip' => $extension,
                                        'hora_atencion' => Carbon::now(),
                                    ]);
                                    $command->info("[{$linkedId}] Llamada atendida por agente: {$agente->name} (ID: {$agente->id}, Ext: {$extension})");

                                    // Dispara evento al agente específico
                                    event(new LlamadaActualizada($llamada->fresh(), $agente->id));
                                    Log::info("[{$linkedId}] LlamadaActualizada evento (BridgeEnter) disparado para agente ID: {$agente->id}");
                                } else {
                                    Log::error("[{$linkedId}] BridgeEnterEvent: No se encontró un Agente con extensión SIP '{$extension}' en la base de datos.");
                                }
                            } else {
                                Log::warning("[{$linkedId}] BridgeEnterEvent: Llamada no encontrada o ya finalizada/perdida en la BD.");
                            }
                        } else {
                            Log::info("BridgeEnterEvent: Sin extensión o Linkedid válido. No se puede procesar la asignación de agente.");
                        }
                    }

                    // --- Evento: Cola llama a un agente específico ---
                    if ($event instanceof AgentCalledEvent) {
                        $command->handleAgentCalled($event);
                    }

                    // --- Evento: Agente no contesta ---
                    if ($event instanceof AgentRingNoAnswerEvent) {
                        $command->handleAgentRingNoAnswer($event);
                    }

                    // --- Evento: Llamada finalizada ---
                    if ($event instanceof HangupEvent) {
                        $uniqueId = $event->getUniqueId();
                        $linkedId = $event->getKey('Linkedid');
                        $causeTxt = $event->getKey('Cause-txt') ?? 'Desconocida';

                        $idToSearch = $linkedId ?? $uniqueId;

                        if ($idToSearch) {
                            $llamada = Llamada::where('unique_id_asterisk', $idToSearch)
                                ->whereNotIn('estado', ['finalizada', 'perdida'])
                                ->first();

                            if ($llamada) {
                                $estadoFinal = $llamada->hora_atencion ? 'finalizada' : 'perdida';
                                $llamada->update([
                                    'estado'    => $estadoFinal,
                                    'hora_fin'  => Carbon::now(),
                                    'causa_fin' => $causeTxt,
                                ]);

                                $command->warn("[{$idToSearch}] Estado de la llamada actualizado a: {$estadoFinal} (Causa: {$causeTxt}).");

                                // Disparar evento si había agente asignado
                                if ($llamada->agente_id) {
                                    event(new LlamadaActualizada($llamada->fresh(), $llamada->agente_id));
                                    Log::info("[{$idToSearch}] LlamadaActualizada evento (Hangup) disparado para agente ID: {$llamada->agente_id}");
                                } else {
                                    // Disparar evento general para que todos los agentes oculten el panel
                                    event(new LlamadaActualizada($llamada->fresh(), null));
                                    Log::info("[{$idToSearch}] Llamada finalizada/perdida sin agente asignado. Evento general disparado.");
                                }
                            } else {
                                Log::info("[{$idToSearch}] HangupEvent: Llamada no encontrada o ya terminada en la BD.");
                            }
                        } else {
                            Log::info("HangupEvent: Sin uniqueId/linkedId válido. No se puede procesar la finalización de llamada.");
                        }
                    }
                });

                while (true) {
                    $pamiClient->process();
                    usleep(1000);
                }

            } catch (Exception $e) {
                $this->error("Error en la conexión con AMI: " . $e->getMessage());
                Log::error("Error en AsteriskListen: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                if (isset($pamiClient)) {
                    $pamiClient->close();
                }
                $this->warn("Intentando reconectar en 10 segundos...");
                sleep(10);
            }
        }
    }

    // --- MÉTODOS MANEJADORES ---
    private function handleAgentCalled(AgentCalledEvent $event): void
{
    $uniqueId = $event->getKey('Uniqueid');
    $agentChannel = $event->getKey('Interface') ?? $event->getKey('Channel'); // Compatibilidad
    
    if (preg_match('/^(?:PJSIP|SIP)\/(\d+)/', $agentChannel, $matches)) {
        $extension = $matches[1];
        $llamada = Llamada::where('unique_id_asterisk', $uniqueId)->first();
        $agente = User::where('extension_sip', $extension)->first();

        if ($llamada && $agente) {
            $this->info("[{$uniqueId}] Cola llamando al agente {$agente->name} ({$extension}). Notificando...");
            
            //Actualizar la BD para asignar la llamada al agente mientras está sonando
            $llamada->update([
                'estado' => 'sonando',
                'agente_id' => $agente->id, 
                'extension_sip' => $extension
                // No reseteamos hora_atencion para recordar que ya sonó previamente
            ]);
            
            // IMPORTANTE: Enviar la notificación por AMBOS canales para garantizar recepción
            event(new LlamadaActualizada($llamada->fresh(), $agente->id));
            $this->info("[{$uniqueId}] Eventos disparados para llamada asignada a agente ID: {$agente->id}");
        } else {
            if (!$llamada) {
                Log::warning("[{$uniqueId}] AgentCalledEvent: No se encontró la llamada en la BD.");
            }
            if (!$agente) {
                Log::warning("[{$uniqueId}] AgentCalledEvent: No se encontró agente con extensión {$extension}.");
            }
        }
    } else {
        Log::warning("AgentCalledEvent: No se pudo extraer extensión de {$agentChannel}");
    }
}

    private function handleAgentRingNoAnswer(AgentRingNoAnswerEvent $event): void
    {
        $uniqueId = $event->getKey('Uniqueid');
        $agentChannel = $event->getKey('Interface') ?? $event->getKey('Channel');

        if (preg_match('/^(?:PJSIP|SIP)\/(\d+)/', $agentChannel, $matches)) {
            $extension = $matches[1];
            $agente = User::where('extension_sip', $extension)->first();

            if ($uniqueId && $agente) {
                $this->info("[{$uniqueId}] Agente {$agente->name} no contestó. Ocultando panel...");
                
                // PASO 1: Enviar evento para ocultar el panel solo al agente que no contestó
                event(new LlamadaTerminadaParaAgente($uniqueId, $agente->id));
                Log::info("[{$uniqueId}] LlamadaTerminadaParaAgente evento disparado para agente ID: {$agente->id}");
                
                // PASO 2: Actualizar llamada en BD (vuelve a la cola)
                $llamada = Llamada::where('unique_id_asterisk', $uniqueId)->first();
                if ($llamada) {
                    // Si la llamada estaba asignada a este agente, la devolvemos a la cola
                    if ($llamada->agente_id == $agente->id) {
                        $llamada->update([
                            'estado' => 'sonando',
                            'agente_id' => null,
                            'extension_sip' => null
                            // No reseteamos hora_atencion para saber que ya fue atendida previamente
                        ]);
                        $this->info("[{$uniqueId}] Llamada devuelta a cola");
                        
                        // PASO 3: Notificar a todos los agentes que hay una llamada en cola
                        event(new LlamadaActualizada($llamada->fresh(), null));
                        Log::info("[{$uniqueId}] Llamada notificada a todos los agentes (cola)");
                    }
                }
            } else {
                if (!$agente) {
                    Log::warning("[{$uniqueId}] AgentRingNoAnswerEvent: No se encontró agente con extensión {$extension}.");
                }
                if (!$uniqueId) {
                    Log::warning("AgentRingNoAnswerEvent: No se encontró uniqueId en el evento.");
                }
            }
        } else {
            Log::warning("AgentRingNoAnswerEvent: No se pudo extraer extensión de {$agentChannel}");
        }
    }
}