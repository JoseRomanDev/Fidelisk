<div x-data>
    
    <!--[if BLOCK]><![endif]--><?php if($llamadaActiva): ?>
        <div class="fixed bottom-5 right-5 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-2xl border-2
            <?php if($llamadaActiva['estado'] === 'sonando'): ?> border-blue-500 
            <?php elseif($llamadaActiva['estado'] === 'en_curso'): ?> border-green-500 
            <?php elseif($llamadaActiva['estado'] === 'en_cola'): ?> border-yellow-500 
            <?php else: ?> border-gray-300 <?php endif; ?>"
             wire:key="<?php echo e($llamadaActiva['unique_id_asterisk']); ?>">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            <!--[if BLOCK]><![endif]--><?php if($llamadaActiva['estado'] === 'sonando'): ?>
                                Llamada Entrante...
                            <?php elseif($llamadaActiva['estado'] === 'en_curso'): ?>
                                Llamada en Curso
                            <?php elseif($llamadaActiva['estado'] === 'en_cola'): ?>
                                Llamada en Cola 
                            <?php else: ?>
                                Llamada <?php echo e(ucfirst($llamadaActiva['estado'])); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h3>
                        <p class="mt-1 text-2xl font-mono text-gray-700 dark:text-gray-300"><?php echo e($llamadaActiva['caller_id_num']); ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($llamadaActiva['caller_id_name'] ?: 'Desconocido'); ?></p>
                    </div>
                    <div class="px-2 py-1 text-xs font-semibold uppercase rounded-full
                        <?php if($llamadaActiva['estado'] === 'sonando'): ?> bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-100
                        <?php elseif($llamadaActiva['estado'] === 'en_curso'): ?> bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100
                        <?php elseif($llamadaActiva['estado'] === 'en_cola'): ?> bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                        <?php else: ?> bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100 <?php endif; ?>">
                        <?php echo e(str_replace('_', ' ', $llamadaActiva['estado'])); ?>

                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <!--[if BLOCK]><![endif]--><?php if($clienteAsociado): ?>
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Cliente Asociado:</p>
                            <p class="text-md text-gray-900 dark:text-white"><?php echo e($clienteAsociado->nombre); ?> <?php echo e($clienteAsociado->apellidos); ?></p>
                            <button wire:click="abrirModalVerCliente" class="mt-1 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Ver/Editar Detalles del Cliente
                            </button>
                        </div>
                    <?php else: ?>
                        <button wire:click="abrirModalCrearCliente" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800">
                            <i class="fas fa-plus mr-2"></i> Crear Nuevo Cliente
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <!--[if BLOCK]><![endif]--><?php if(in_array($llamadaActiva['estado'], ['sonando', 'en_curso']) && $llamadaActiva['agente_id'] == Auth::id()): ?>
                    <div class="mt-4">
                        
                        
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($showCrearClienteModal): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="$wire.cerrarModalCrearCliente()">
                <form wire:submit.prevent="guardarNuevoCliente">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Crear Nuevo Cliente</h3>
                        <div class="mt-4 space-y-4">
                            
                            <div>
                                <label for="nombre_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></label>
                                <input wire:model.defer="nombre" type="text" id="nombre_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="apellidos_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                                <input wire:model.defer="apellidos" type="text" id="apellidos_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['apellidos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['apellidos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="telefono_principal_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono Principal <span class="text-red-500">*</span></label>
                                <input wire:model.defer="telefono_principal" type="text" id="telefono_principal_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['telefono_principal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['telefono_principal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="email_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input wire:model.defer="email" type="email" id="email_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
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
                            <!--[if BLOCK]><![endif]--><?php if($es_contacto_empresa): ?>
                                <div>
                                    <label for="nombre_empresa_representada_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Empresa <span class="text-red-500">*</span></label>
                                    <input wire:model.defer="nombre_empresa_representada" type="text" id="nombre_empresa_representada_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['nombre_empresa_representada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nombre_empresa_representada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div>
                                    <label for="puesto_contacto_empresa_crear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto en la Empresa</label>
                                    <input wire:model.defer="puesto_contacto_empresa" type="text" id="puesto_contacto_empresa_crear" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($showVerClienteModal && $clienteParaVer): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="$wire.cerrarModalVerCliente()">
                <form wire:submit.prevent="actualizarClienteExistente">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detalles del Cliente</h3>
                        <div class="mt-4 space-y-4">
                             
                            <div>
                                <label for="nombre_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></label>
                                <input wire:model.defer="nombre" type="text" id="nombre_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="apellidos_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                                <input wire:model.defer="apellidos" type="text" id="apellidos_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['apellidos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['apellidos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="telefono_principal_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono Principal <span class="text-red-500">*</span></label>
                                <input wire:model.defer="telefono_principal" type="text" id="telefono_principal_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['telefono_principal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['telefono_principal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="email_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input wire:model.defer="email" type="email" id="email_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
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
                            <!--[if BLOCK]><![endif]--><?php if($es_contacto_empresa): ?>
                                <div>
                                    <label for="nombre_empresa_representada_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Empresa <span class="text-red-500">*</span></label>
                                    <input wire:model.defer="nombre_empresa_representada" type="text" id="nombre_empresa_representada_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['nombre_empresa_representada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nombre_empresa_representada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div>
                                    <label for="puesto_contacto_empresa_ver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puesto en la Empresa</label>
                                    <input wire:model.defer="puesto_contacto_empresa" type="text" id="puesto_contacto_empresa_ver" class="block w-full mt-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
             class="fixed top-5 right-5 bg-green-500 text-white py-2 px-4 rounded-xl text-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full">
            <p><?php echo e(session('message')); ?></p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php if(session()->has('error')): ?>
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
             class="fixed top-5 right-5 bg-red-500 text-white py-2 px-4 rounded-xl text-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full">
            <p><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\josem\OneDrive\Escritorio\LARAVEL\Fidelisk\resources\views/livewire/agente/llamada-activa-panel.blade.php ENDPATH**/ ?>