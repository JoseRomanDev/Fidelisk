<div>
    {{-- Contenedor principal de la gestión de usuarios --}}
    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800/50 rounded-lg shadow-md">
        
        {{-- Encabezado y Botón de Crear Usuario --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                    Gestión de Usuarios y Roles
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Busca, crea y edita los usuarios y sus roles asignados.
                </p>
            </div>
            <div class="mt-3 sm:mt-0">
                <button wire:click="openCreateModal" type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Crear Usuario
                </button>
            </div>
        </div>
        
        {{-- Barra de Búsqueda --}}
        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o email..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
        </div>

        {{-- Mensaje de estado --}}
        @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-800/30">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('message') }}
                </p>
            </div>
        @endif
        {{-- Tabla de Usuarios --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrado</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 ml-2">({{ $user->email }})</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->roles as $role)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($role->name == 'admin') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($role->name == 'supervisor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="editUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Editar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron usuarios que coincidan con la búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal de Creación de Usuario --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" x-data @click.self="$wire.closeCreateModal()">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="$wire.closeCreateModal()">
                {{-- La llamada al componente se encarga de mostrar el formulario de creación --}}
                @livewire('admin.create-user-form')
            </div>
        </div>
    @endif

    {{-- Modal de Edición de Usuario --}}
@if($showEditModal && $editingUser)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" x-data @click.self="$wire.closeEditModal()">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="$wire.closeEditModal()">
            <form wire:submit.prevent="updateUser">
                <div class="p-6 space-y-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        Editar Usuario: <span class="font-bold">{{ $editingUserData['name'] }}</span>
                    </h3>
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                        <input wire:model.defer="editingUserData.name" type="text" id="edit_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200" />
                        @error('editingUserData.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input wire:model.defer="editingUserData.email" type="email" id="edit_email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200" />
                        @error('editingUserData.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_extension_sip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Extensión SIP</label>
                        <input wire:model.defer="editingUserData.extension_sip" type="text" id="edit_extension_sip" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200" />
                        @error('editingUserData.extension_sip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                        <input wire:model.defer="editingUserPassword" type="password" id="edit_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200" autocomplete="new-password" />
                        @error('editingUserPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                        <input wire:model.defer="editingUserPassword_confirmation" type="password" id="edit_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200" autocomplete="new-password" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Roles</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($allRoles as $role)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model.defer="userRoles" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-600">
                                    <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($role->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('userRoles') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3 flex justify-end space-x-3">
                    <button type="button" wire:click="closeEditModal" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
</div>