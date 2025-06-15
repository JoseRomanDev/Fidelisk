<div class="p-4 sm:p-6 bg-white dark:bg-gray-800/50 rounded-lg shadow-md mt-6">
    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
        Crear Nuevo Usuario
    </h3>
    <form wire:submit.prevent="saveUser" class="mt-4 space-y-4">
        {{-- Nombre --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
            <input wire:model="name" type="text" id="name" class="mt-1 block w-full rounded-md ...">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input wire:model="email" type="email" id="email" class="mt-1 block w-full rounded-md ...">
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
            <input wire:model="password" type="password" id="password" class="mt-1 block w-full rounded-md ...">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Confirmar Contraseña --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="mt-1 block w-full rounded-md ...">
        </div>

        {{-- Asignar Roles --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($allRoles as $role)
                    <label class="flex items-center space-x-3 ...">
                        <input type="checkbox" wire:model="rolesToAssign" value="{{ $role->id }}" class="rounded ...">
                        <span>{{ ucfirst($role->name) }}</span>
                    </label>
                @endforeach
            </div>
            @error('rolesToAssign') <span class="text-red-500 text-xs mt-2">{{ $message }}</span> @enderror
        </div>
        
        {{-- Asignar Extensión SIP --}}
        <div>
            <label for="extension_sip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Extensión SIP</label>
            <input wire:model="extension_sip" type="text" id="extension_sip" class="mt-1 block w-full rounded-md ...">
            @error('extension_sip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Botón de Guardar --}}
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Crear Usuario
            </button>
        </div>
    </form>
</div>