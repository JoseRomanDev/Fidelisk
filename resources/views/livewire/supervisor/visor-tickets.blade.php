{{-- resources/views/livewire/supervisor/visor-tickets.blade.php --}}
<div class="space-y-6">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
        Visor Global de Tickets
    </h3>

    {{-- Sección de Filtros --}}
    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            {{-- Búsqueda General --}}
            <div>
                <label for="busquedaGeneralSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Búsqueda General</label>
                <input type="text" wire:model.live.debounce.300ms="busquedaGeneral" id="busquedaGeneralSupervisor"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 py-3 px-4"
                       placeholder="Asunto, descripción, cliente, agente...">
            </div>

            {{-- Filtro por Agente --}}
            <div>
                <label for="filtroAgenteIdSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agente</label>
                <select wire:model.live="filtroAgenteId" id="filtroAgenteIdSupervisor"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-3 px-4 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todos los Agentes</option>
                    @foreach($agentesParaFiltrar as $agente)
                        <option value="{{ $agente->id }}">{{ $agente->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Cliente --}}
            <div>
                <label for="filtroClienteIdSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cliente</label>
                <select wire:model.live="filtroAgenteId" id="filtroAgenteIdSupervisor"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-3 px-4 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todos los Clientes</option>
                    @foreach($clientesParaFiltrar as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellidos }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Estado --}}
            <div>
                <label for="filtroEstadoSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                <select wire:model.live="filtroAgenteId" id="filtroAgenteIdSupervisor"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-3 px-4 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todos los Estados</option>
                    @foreach($estadosPosibles as $estado)
                        <option value="{{ $estado }}">{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Prioridad --}}
            <div>
                <label for="filtroPrioridadSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                <select wire:model.live="filtroAgenteId" id="filtroAgenteIdSupervisor"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-3 px-4 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Todas las Prioridades</option>
                     @foreach($prioridadesPosibles as $prioridad)
                        <option value="{{ $prioridad }}">{{ ucfirst($prioridad) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Fecha Desde --}}
            <div>
                <label for="fechaDesdeSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Desde</label>
                <input type="date" wire:model.live="fechaDesde" id="fechaDesdeSupervisor"
                       class="mt-3 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
            </div>

            {{-- Filtro Fecha Hasta --}}
            <div>
                <label for="fechaHastaSupervisor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Hasta</label>
                <input type="date" wire:model.live="fechaHasta" id="fechaHastaSupervisor"
                       class="mt-3 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
            </div>
            
            {{-- Botón Limpiar Filtros --}}
            <div class="flex items-end">
                 <button wire:click="limpiarFiltros"
                        type="button"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de Tickets --}}
    <div class="flex flex-col mt-2">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Asunto</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ag. Creador</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ag. Asignado</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prioridad</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creado</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Resuelto</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $ticket->id }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span title="{{ $ticket->asunto }}">{{ Str::limit($ticket->asunto, 25) }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $ticket->cliente ? Str::limit($ticket->cliente->nombre . ' ' . ($ticket->cliente->apellidos ?? ''), 20, '...') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticket->agenteCreador ? Str::limit($ticket->agenteCreador->name, 15, '...') : 'N/A' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticket->agenteAsignado ? Str::limit($ticket->agenteAsignado->name, 15, '...') : 'Sin asignar' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @switch($ticket->estado)
                                                @case('abierto') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                                @case('en_proceso') bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100 @break
                                                @case('pendiente_cliente') bg-orange-100 text-orange-800 dark:bg-orange-600 dark:text-orange-100 @break
                                                @case('resuelto') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                                @case('cerrado') bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                                @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                            @endswitch">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->estado)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @switch($ticket->prioridad)
                                                @case('baja') bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                                @case('media') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                                @case('alta') bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100 @break
                                                @case('urgente') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                                @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                            @endswitch">
                                            {{ ucfirst($ticket->prioridad) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" title="{{ $ticket->created_at->toDayDateTimeString() }}">{{ $ticket->created_at->diffForHumans() }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" title="{{ $ticket->fecha_resolucion ? \Carbon\Carbon::parse($ticket->fecha_resolucion)->toDayDateTimeString() : '' }}">
                                        {{ $ticket->fecha_resolucion ? \Carbon\Carbon::parse($ticket->fecha_resolucion)->diffForHumans() : '-' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium space-x-1">
                                        <button wire:click="verDetallesTicket({{ $ticket->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" 
                                                title="Ver Detalles del Ticket">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button wire:click="abrirModalReasignar({{ $ticket->id }})" {{-- NUEVO BOTÓN --}}
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" 
                                                title="Reasignar Ticket">
                                            <i class="fas fa-random"></i> {{-- O fas fa-exchange-alt --}}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 whitespace-nowrap text-lg text-center text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                            No se encontraron tickets con los filtros actuales.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    @if ($tickets->hasPages())
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @endif

    {{-- Modal para Ver Detalles del Ticket --}}
    @if ($showViewTicketModal && $selectedTicketDetails)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="viewTicketModalLabel" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div wire:click="closeViewTicketModal()" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>

            {{-- Contenido del Modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100" id="viewTicketModalLabel">
                        Detalles del Ticket #{{ $selectedTicketDetails->id }}
                    </h3>
                    <button type="button" wire:click="closeViewTicketModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>

                <div class="mt-6 space-y-4">
                    {{-- Detalles del Ticket --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Asunto</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedTicketDetails->asunto }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioridad</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($selectedTicketDetails->prioridad)
                                        @case('baja') bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                        @case('media') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                        @case('alta') bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100 @break
                                        @case('urgente') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                    @endswitch">
                                    {{ ucfirst($selectedTicketDetails->prioridad) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</h4>
                             <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($selectedTicketDetails->estado)
                                        @case('abierto') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                        @case('en_proceso') bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100 @break
                                        @case('pendiente_cliente') bg-orange-100 text-orange-800 dark:bg-orange-600 dark:text-orange-100 @break
                                        @case('resuelto') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                        @case('cerrado') bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                    @endswitch">
                                    {{ ucfirst(str_replace('_', ' ', $selectedTicketDetails->estado)) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $selectedTicketDetails->cliente->nombre ?? 'N/A' }} {{ $selectedTicketDetails->cliente->apellidos ?? '' }} <br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedTicketDetails->cliente->email ?? '' }}</span> <br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Tel: {{ $selectedTicketDetails->cliente->telefono_principal ?? '' }}</span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Agente Creador</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedTicketDetails->agenteCreador->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Agente Asignado</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedTicketDetails->agenteAsignado->name ?? 'Sin asignar' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</h4>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $selectedTicketDetails->descripcion }}</p>
                    </div>

                    @if($selectedTicketDetails->solucion_aplicada)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Solución Aplicada</h4>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $selectedTicketDetails->solucion_aplicada }}</p>
                    </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha Creación</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" title="{{ $selectedTicketDetails->created_at->toDayDateTimeString() }}">
                                {{ $selectedTicketDetails->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" title="{{ $selectedTicketDetails->updated_at->toDayDateTimeString() }}">
                                {{ $selectedTicketDetails->updated_at->diffForHumans() }}
                            </p>
                        </div>
                        @if($selectedTicketDetails->fecha_resolucion)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha Resolución</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" title="{{ \Carbon\Carbon::parse($selectedTicketDetails->fecha_resolucion)->toDayDateTimeString() }}">
                                {{ \Carbon\Carbon::parse($selectedTicketDetails->fecha_resolucion)->diffForHumans() }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end">
                        <button type="button" wire:click="closeViewTicketModal()"
                                class="rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- ... (Modal de Ver Detalles existente) ... --}}

    {{-- Modal para Reasignar Ticket --}}
    @if ($showReasignarModal && $ticketParaReasignar)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="reasignarModalLabel" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div wire:click="closeReasignarModal()" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>

            {{-- Contenido del Modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <form wire:submit.prevent="reasignarTicketSeleccionado">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="reasignarModalLabel">
                            Reasignar Ticket #{{ $ticketParaReasignar->id }}
                        </h3>
                        <button type="button" wire:click="closeReasignarModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Cerrar modal</span>
                        </button>
                    </div>

                    <div class="mt-6 space-y-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Asunto: <span class="font-semibold">{{ $ticketParaReasignar->asunto }}</span>
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Cliente: <span class="font-semibold">{{ $ticketParaReasignar->cliente->nombre ?? 'N/A' }} {{ $ticketParaReasignar->cliente->apellidos ?? '' }}</span>
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Agente Asignado Actual: <span class="font-semibold">{{ $ticketParaReasignar->agenteAsignado->name ?? 'Sin asignar' }}</span>
                        </p>
                        
                        <div>
                            <label for="nuevoAgenteIdParaReasignar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seleccionar Nuevo Agente <span class="text-red-500">*</span></label>
                            <select wire:model.defer="nuevoAgenteIdParaReasignar" id="nuevoAgenteIdParaReasignar" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('nuevoAgenteIdParaReasignar') border-red-500 dark:border-red-500 @enderror">
                                <option value="">-- Seleccione un agente --</option>
                                @foreach($agentesParaFiltrar as $agente)
                                    <option value="{{ $agente->id }}">{{ $agente->name }}</option>
                                @endforeach
                            </select>
                            @error('nuevoAgenteIdParaReasignar') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeReasignarModal()"
                                    class="rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <div wire:loading wire:target="reasignarTicketSeleccionado" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Confirmar Reasignación
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
</div>