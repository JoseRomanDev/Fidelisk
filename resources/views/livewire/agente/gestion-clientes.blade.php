@php
    use Carbon\Carbon;
@endphp
<div class="space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate">
                Listado de Clientes
            </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <div class="flex-1">
                <label for="busqueda_clientes" class="sr-only">Buscar clientes</label>
                <input type="text" wire:model.live.debounce.300ms="busqueda" id="busqueda_clientes"
                       class="py-4 px-4 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200"
                       placeholder="Buscar por nombre, teléfono, email...">
            </div>
            <button wire:click="crearNuevoCliente()"
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                <i class="fas fa-plus me-2"></i> Crear Nuevo Cliente
            </button>
        </div>
    </div>

    {{-- Mensajes Flash --}}
    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 dark:bg-green-800/30">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.06 0l4.002-5.496z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" @click="open = false" {{-- Assuming Alpine.js for simple close, or handle with Livewire --}}
                                class="inline-flex rounded-md bg-green-50 dark:bg-transparent p-1.5 text-green-500 dark:text-green-600 hover:bg-green-100 dark:hover:bg-green-700/50 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-gray-800">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
    <div class="rounded-md bg-red-50 p-4 dark:bg-red-800/30">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400 dark:text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
             <div class="ml-auto pl-3"> <div class="-mx-1.5 -my-1.5"> <button type="button" class="inline-flex rounded-md bg-red-50 dark:bg-transparent p-1.5 text-red-500 dark:text-red-600 hover:bg-red-100 dark:hover:bg-red-700/50 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50 dark:focus:ring-offset-gray-800"><span class="sr-only">Dismiss</span><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg></button> </div> </div>
        </div>
    </div>
    @endif

    {{-- Modal para Crear o Editar Cliente --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="clienteModalLabel" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div wire:click="closeModal()" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            {{-- Contenido del Modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <form wire:submit.prevent="guardarCliente">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="clienteModalLabel">
                            {{ $cliente_id ? 'Editar Cliente' : 'Crear Nuevo Cliente' }}
                        </h3>
                        <button type="button" wire:click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Cerrar modal</span>
                        </button>
                    </div>

                    <div class="mt-6 space-y-6">
                       
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="nombre" id="nombre" autocomplete="off"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('nombre') border-red-500 @enderror">
                                @error('nombre') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="apellidos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                                <input type="text" wire:model.defer="apellidos" id="apellidos" autocomplete="off"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('apellidos') border-red-500 @enderror">
                                @error('apellidos') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                         <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="telefono_principal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono Principal <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="telefono_principal" id="telefono_principal" autocomplete="off"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('telefono_principal') border-red-500 @enderror">
                                @error('telefono_principal') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" wire:model.defer="email" id="email" autocomplete="off"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('email') border-red-500 @enderror">
                                @error('email') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="direccion_completa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección Completa</label>
                            <textarea wire:model.defer="direccion_completa" id="direccion_completa" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('direccion_completa') border-red-500 @enderror"></textarea>
                            @error('direccion_completa') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="notas_agente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas del Agente</label>
                            <textarea wire:model.defer="notas_agente" id="notas_agente" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('notas_agente') border-red-500 @enderror"></textarea>
                            @error('notas_agente') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <hr class="dark:border-gray-600">
                        <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300">Información de Empresa (si aplica)</h6>
                        <div class="flex items-center">
                            <input id="es_contacto_empresa" wire:model.live="es_contacto_empresa" type="checkbox" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-indigo-600 dark:ring-offset-gray-800">
                            <label for="es_contacto_empresa" class="ml-2 block text-sm text-gray-900 dark:text-gray-200">Es contacto de una empresa</label>
                        </div>

                        @if ($es_contacto_empresa)
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="nombre_empresa_representada" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Empresa <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model.defer="nombre_empresa_representada" id="nombre_empresa_representada" autocomplete="off"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('nombre_empresa_representada') border-red-500 @enderror">
                                    @error('nombre_empresa_representada') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="puesto_contacto_empresa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto del Contacto</label>
                                    <input type="text" wire:model.defer="puesto_contacto_empresa" id="puesto_contacto_empresa" autocomplete="off"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('puesto_contacto_empresa') border-red-500 @enderror">
                                    @error('puesto_contacto_empresa') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif

                        @if($cliente_id)
                        <div>
                            <label for="estado_cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                            <select wire:model.defer="estado" id="estado_cliente" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('estado') border-red-500 @enderror">
                                <option value="activo">Activo</option>
                                <option value="dado_de_baja">Dado de Baja</option>
                            </select>
                            @error('estado') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
                                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <div wire:loading wire:target="guardarCliente" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                {{ $cliente_id ? 'Actualizar Cliente' : 'Guardar Cliente' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabla de Clientes --}}
    <div class="flex flex-col mt-2">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Apellidos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teléfono</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Empresa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $cliente->nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $cliente->apellidos ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $cliente->telefono_principal }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $cliente->email ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $cliente->es_contacto_empresa ? $cliente->nombre_empresa_representada : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $cliente->estado == 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' }}">
                                            {{ ucfirst(str_replace('_', ' ', $cliente->estado)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button wire:click="editarCliente({{ $cliente->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if ($cliente->estado == 'activo')
                                            <button wire:click="confirmarDarDeBaja({{ $cliente->id }})" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Dar de Baja">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        @else
                                            <button wire:click="confirmarReactivar({{ $cliente->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Reactivar">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">
                                        No se encontraron clientes.
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
    @if ($clientes->hasPages())
        <div class="mt-4">
            {{-- Para personalizar la paginación para Tailwind, ejecuta: --}}
            {{-- php artisan vendor:publish --tag=laravel-pagination --}}
            {{-- y luego edita las vistas en resources/views/vendor/pagination --}}
            {{-- O usa Livewire\WithPagination y personaliza sus vistas de paginación --}}
            {{ $clientes->links() }} {{-- Esto usará las vistas de paginación por defecto de Laravel/Livewire --}}
        </div>
    @endif

    {{-- Modales de Confirmación (Dar de Baja / Reactivar) --}}
    {{-- Estilo similar al modal principal --}}
    @if ($showConfirmacionBajaModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="confirmBajaModalLabel" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="$set('showConfirmacionBajaModal', false)" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                     <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="confirmBajaModalLabel">Confirmar Dar de Baja</h3>
                     <button type="button" wire:click="$set('showConfirmacionBajaModal', false)" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300">¿Estás seguro de que deseas dar de baja a este cliente? Esta acción cambiará su estado y registrará la fecha de baja.</p>
                </div>
                <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showConfirmacionBajaModal', false)"
                                class="rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            Cancelar
                        </button>
                        <button type="button" wire:click="darDeBajaCliente()"
                                class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <div wire:loading wire:target="darDeBajaCliente" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            Sí, Dar de Baja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($showConfirmacionReactivarModal)
        {{-- Estructura similar al modal de dar de baja, pero con textos y botón de "Reactivar" (ej. bg-green-600) --}}
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="confirmReactivarModalLabel" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="$set('showConfirmacionReactivarModal', false)" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                         <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="confirmReactivarModalLabel">Confirmar Reactivación</h3>
                         <button type="button" wire:click="$set('showConfirmacionReactivarModal', false)" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Cerrar modal</span>
                        </button>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">¿Estás seguro de que deseas reactivar a este cliente? Su estado volverá a ser "activo".</p>
                    </div>
                    <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showConfirmacionReactivarModal', false)"
                                    class="rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Cancelar
                            </button>
                            <button type="button" wire:click="reactivarCliente()"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <div wire:loading wire:target="reactivarCliente" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Sí, Reactivar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>