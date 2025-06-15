<div x-data>
    {{-- Notificación de llamada --}}
    @if ($llamadaActiva)
        <div class="fixed bottom-5 right-5 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-2xl border-2
            @if($llamadaActiva['estado'] === 'sonando') border-blue-500 
            @elseif($llamadaActiva['estado'] === 'en_curso') border-green-500 
            @elseif($llamadaActiva['estado'] === 'en_cola') border-yellow-500 
            @else border-gray-300 @endif"
             wire:key="{{ $llamadaActiva['unique_id_asterisk'] }}">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            @if ($llamadaActiva['estado'] === 'sonando')
                                Llamada Entrante...
                            @elseif ($llamadaActiva['estado'] === 'en_curso')
                                Llamada en Curso
                            @elseif ($llamadaActiva['estado'] === 'en_cola')
                                Llamada en Cola 
                            @else
                                Llamada {{ ucfirst($llamadaActiva['estado']) }}
                            @endif
                        </h3>
                        <p class="mt-1 text-2xl font-mono text-gray-700 dark:text-gray-300">{{ $llamadaActiva['caller_id_num'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $llamadaActiva['caller_id_name'] ?: 'Desconocido' }}</p>
                    </div>
                    <div class="px-2 py-1 text-xs font-semibold uppercase rounded-full
                        @if($llamadaActiva['estado'] === 'sonando') bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-100
                        @elseif($llamadaActiva['estado'] === 'en_curso') bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100
                        @elseif($llamadaActiva['estado'] === 'en_cola') bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                        @else bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100 @endif">
                        {{ str_replace('_', ' ', $llamadaActiva['estado']) }}
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    @if ($clienteAsociado)
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Cliente Asociado:</p>
                            <p class="text-md text-gray-900 dark:text-white">{{ $clienteAsociado->nombre }} {{ $clienteAsociado->apellidos }}</p>
                            <button wire:click="abrirModalVerCliente" class="mt-1 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Ver/Editar Detalles del Cliente
                            </button>
                        </div>
                    @else
                        <button wire:click="abrirModalCrearCliente" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800">
                            <i class="fas fa-plus mr-2"></i> Crear Nuevo Cliente
                        </button>
                    @endif
                </div>

               
                @if (in_array($llamadaActiva['estado'], ['sonando', 'en_curso']) && $llamadaActiva['agente_id'] == Auth::id())
                    <div class="mt-4">
                        
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Modal para Crear Nuevo Cliente --}}
    @if ($showCrearClienteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="$wire.cerrarModalCrearCliente()">
                <form wire:submit.prevent="guardarNuevoCliente">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Crear Nuevo Cliente</h3>
                        <div class="mt-4 space-y-4">
                            {{-- Campos del formulario de cliente --}}
                            <div>
                                <label for="nombre_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></label>
                                <input wire:model.defer="nombre" type="text" id="nombre_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('nombre') border-red-500 @enderror">
                                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="apellidos_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                                <input wire:model.defer="apellidos" type="text" id="apellidos_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('apellidos') border-red-500 @enderror">
                                @error('apellidos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="telefono_principal_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono Principal <span class="text-red-500">*</span></label>
                                <input wire:model.defer="telefono_principal" type="text" id="telefono_principal_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('telefono_principal') border-red-500 @enderror">
                                @error('telefono_principal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input wire:model.defer="email" type="email" id="email_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="direccion_completa_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección Completa</label>
                                <textarea wire:model.defer="direccion_completa" id="direccion_completa_crear" rows="3" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            <div>
                                <label for="notas_agente_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas del Agente</label>
                                <textarea wire:model.defer="notas_agente" id="notas_agente_crear" rows="3" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                             <div>
                                <label for="estado_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                <select wire:model.defer="estado" id="estado_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="activo">Activo</option>
                                    <option value="dado_de_baja">Dado de Baja</option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input wire:model.live="es_contacto_empresa" type="checkbox" id="es_contacto_empresa_crear" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="es_contacto_empresa_crear" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Es contacto de empresa</label>
                            </div>
                            @if ($es_contacto_empresa)
                                <div>
                                    <label for="nombre_empresa_representada_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Empresa <span class="text-red-500">*</span></label>
                                    <input wire:model.defer="nombre_empresa_representada" type="text" id="nombre_empresa_representada_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('nombre_empresa_representada') border-red-500 @enderror">
                                    @error('nombre_empresa_representada') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="puesto_contacto_empresa_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto en la Empresa</label>
                                    <input wire:model.defer="puesto_contacto_empresa" type="text" id="puesto_contacto_empresa_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 text-right space-x-2">
                        <button type="button" wire:click="cerrarModalCrearCliente()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            <div wire:loading wire:target="guardarNuevoCliente" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2 inline-block"></div>
                            Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal para Ver/Editar Cliente Existente --}}
    @if ($showVerClienteModal && $clienteParaVer)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="$wire.cerrarModalVerCliente()">
                <form wire:submit.prevent="actualizarClienteExistente">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detalles del Cliente</h3>
                        <div class="mt-4 space-y-4">
                             {{-- Campos del formulario de cliente (iguales que el de crear, pero con wire:model a las propiedades del panel) --}}
                            <div>
                                <label for="nombre_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></label>
                                <input wire:model.defer="nombre" type="text" id="nombre_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('nombre') border-red-500 @enderror">
                                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="apellidos_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                                <input wire:model.defer="apellidos" type="text" id="apellidos_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('apellidos') border-red-500 @enderror">
                                @error('apellidos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="telefono_principal_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono Principal <span class="text-red-500">*</span></label>
                                <input wire:model.defer="telefono_principal" type="text" id="telefono_principal_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('telefono_principal') border-red-500 @enderror">
                                @error('telefono_principal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input wire:model.defer="email" type="email" id="email_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="direccion_completa_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección Completa</label>
                                <textarea wire:model.defer="direccion_completa" id="direccion_completa_ver" rows="3" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            <div>
                                <label for="notas_agente_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas del Agente</label>
                                <textarea wire:model.defer="notas_agente" id="notas_agente_ver" rows="3" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                             <div>
                                <label for="estado_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                <select wire:model.defer="estado" id="estado_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="activo">Activo</option>
                                    <option value="dado_de_baja">Dado de Baja</option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input wire:model.live="es_contacto_empresa" type="checkbox" id="es_contacto_empresa_ver" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="es_contacto_empresa_ver" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Es contacto de empresa</label>
                            </div>
                            @if ($es_contacto_empresa)
                                <div>
                                    <label for="nombre_empresa_representada_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Empresa <span class="text-red-500">*</span></label>
                                    <input wire:model.defer="nombre_empresa_representada" type="text" id="nombre_empresa_representada_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 @error('nombre_empresa_representada') border-red-500 @enderror">
                                    @error('nombre_empresa_representada') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="puesto_contacto_empresa_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto en la Empresa</label>
                                    <input wire:model.defer="puesto_contacto_empresa" type="text" id="puesto_contacto_empresa_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 text-right space-x-2">
                        <button type="button" wire:click="cerrarModalVerCliente()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            <div wire:loading wire:target="actualizarClienteExistente" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2 inline-block"></div>
                            Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Toast para mensajes --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
             class="fixed top-5 right-5 bg-green-500 text-white py-2 px-4 rounded-xl text-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
             class="fixed top-5 right-5 bg-red-500 text-white py-2 px-4 rounded-xl text-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full">
            <p>{{ session('error') }}</p>
        </div>
    @endif
</div>