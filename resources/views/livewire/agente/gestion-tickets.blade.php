@php
    use Carbon\Carbon;
@endphp
{{-- resources/views/livewire/gestion-tickets.blade.php con Tailwind CSS --}}
<div class="space-y-6">
    {{-- Fila para Título, Búsqueda y Botón de Crear --}}
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate">
                Gestión de Tickets
            </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <div class="flex-1">
                <label for="busqueda_tickets" class="sr-only">Buscar tickets</label>
                <input type="text" wire:model.live.debounce.300ms="busqueda" id="busqueda_tickets"
                       class="py-4 px-4 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200"
                       placeholder="Buscar por asunto, cliente...">
            </div>
            <button wire:click="crearNuevoTicket()"
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                <i class="fas fa-plus me-2"></i> Crear Nuevo Ticket
            </button>
        </div>
    </div>

    {{-- Mensajes Flash (similar a gestion-clientes) --}}
    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 dark:bg-green-800/30">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.06 0l4.002-5.496z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3"><p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p></div>
                <div class="ml-auto pl-3"><div class="-mx-1.5 -my-1.5"><button type="button" class="inline-flex rounded-md bg-green-50 dark:bg-transparent p-1.5 text-green-500 dark:text-green-600 hover:bg-green-100 dark:hover:bg-green-700/50 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-gray-800"><span class="sr-only">Dismiss</span><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg></button></div></div>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
    <div class="rounded-md bg-red-50 p-4 dark:bg-red-800/30">
        <div class="flex">
            <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400 dark:text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg></div>
            <div class="ml-3"><p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p></div>
            <div class="ml-auto pl-3"><div class="-mx-1.5 -my-1.5"><button type="button" class="inline-flex rounded-md bg-red-50 dark:bg-transparent p-1.5 text-red-500 dark:text-red-600 hover:bg-red-100 dark:hover:bg-red-700/50 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50 dark:focus:ring-offset-gray-800"><span class="sr-only">Dismiss</span><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg></button></div></div>
        </div>
    </div>
    @endif

    {{-- Modal para Crear o Editar Ticket --}}
    @if ($showTicketModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="ticketModalLabel" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="closeModal()" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <form wire:submit.prevent="guardarTicket">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="ticketModalLabel">
                            {{ $ticket_id ? 'Editar Ticket' : 'Crear Nuevo Ticket' }}
                        </h3>
                         <button type="button" wire:click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Cerrar modal</span>
                        </button>
                    </div>

                    <div class="mt-6 space-y-6">
                        <div>
                            <label for="cliente_id_seleccionado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cliente <span class="text-red-500">*</span></label>
                            <select wire:model.defer="cliente_id_seleccionado" id="cliente_id_seleccionado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('cliente_id_seleccionado') border-red-500 @enderror">
                                <option value="">Seleccione un cliente...</option>
                                @foreach ($clientesParaSelect as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellidos }} ({{ $cliente->telefono_principal }})</option>
                                @endforeach
                            </select>
                            @error('cliente_id_seleccionado') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="asunto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asunto <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="asunto" id="asunto" autocomplete="off"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('asunto') border-red-500 @enderror">
                            @error('asunto') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción <span class="text-red-500">*</span></label>
                            <textarea wire:model.defer="descripcion" id="descripcion" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('descripcion') border-red-500 @enderror"></textarea>
                            @error('descripcion') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad <span class="text-red-500">*</span></label>
                                <select wire:model.defer="prioridad" id="prioridad" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('prioridad') border-red-500 @enderror">
                                    <option value="baja">Baja</option>
                                    <option value="media">Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                                @error('prioridad') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="estado_ticket" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado <span class="text-red-500">*</span></label>
                                <select wire:model.defer="estado" id="estado_ticket" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('estado') border-red-500 @enderror">
                                    <option value="abierto">Abierto</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="pendiente_cliente">Pendiente Cliente</option>
                                    @if($ticket_id || in_array($estado, ['resuelto', 'cerrado']))
                                        <option value="resuelto">Resuelto</option>
                                        <option value="cerrado">Cerrado</option>
                                    @endif
                                </select>
                                @error('estado') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="agente_asignado_id_seleccionado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agente Asignado <span class="text-red-500">*</span></label>
                            <select wire:model.defer="agente_asignado_id_seleccionado" id="agente_asignado_id_seleccionado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('agente_asignado_id_seleccionado') border-red-500 @enderror">
                                <option value="">Seleccione un agente...</option>
                                @foreach ($agentesParaSelect as $agente)
                                    <option value="{{ $agente->id }}">{{ $agente->name }}</option>
                                @endforeach
                            </select>
                            @error('agente_asignado_id_seleccionado') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        @if (in_array($estado, ['resuelto', 'cerrado']))
                        <div>
                            <label for="solucion_aplicada" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solución Aplicada @if (in_array($estado, ['resuelto', 'cerrado'])) <span class="text-red-500">*</span>@endif</label>
                            <textarea wire:model.defer="solucion_aplicada" id="solucion_aplicada" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('solucion_aplicada') border-red-500 @enderror"></textarea>
                            @error('solucion_aplicada') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        @if ($ticket_id && $display_fecha_resolucion && in_array($estado, ['resuelto', 'cerrado']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Resolución:</label>
                            <input type="text" value="{{ $display_fecha_resolucion }}" readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm sm:text-sm dark:bg-gray-700 dark:text-gray-200 opacity-75 cursor-not-allowed">
                        </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end space-x-3">
                             <button type="button" wire:click="closeModal()"
                                    class="rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <div wire:loading wire:target="guardarTicket" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                {{ $ticket_id ? 'Actualizar Ticket' : 'Guardar Ticket' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabla de Tickets --}}
    <div class="flex flex-col mt-2">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Asunto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ag. Asignado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prioridad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Resuelto</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $ticket->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ Str::limit($ticket->asunto, 30) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticket->cliente ? Str::limit($ticket->cliente->nombre . ' ' . ($ticket->cliente->apellidos ?? ''), 20, '...') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticket->agenteAsignado ? Str::limit($ticket->agenteAsignado->name, 15, '...') : ($ticket->agenteCreador ? Str::limit($ticket->agenteCreador->name, 10, '...').' (C)' : 'N/A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticket->created_at->format('d/m/y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticket->fecha_resolucion ? Carbon::parse($ticket->fecha_resolucion)->format('d/m/y H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button wire:click="editarTicket({{ $ticket->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Editar/Ver Detalles">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        {{-- Aquí podrían ir más acciones como cambiar estado rápidamente, etc. --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">
                                        No se encontraron tickets.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($tickets->hasPages())
        <div class="mt-4">
            {{ $tickets->links() }} {{-- Considerar personalizar para Tailwind --}}
        </div>
    @endif
</div>