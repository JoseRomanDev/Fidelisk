{{-- resources/views/Admin/panel.blade.php --}}
<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel del Administrador') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
            <h3 class="text-3xl font-medium text-gray-900 dark:text-gray-100 mb-4">
                Bienvenido al Panel del Administrador
            </h3>
        
            
            {{-- Tarjeta para Gestión de Usuarios --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    @livewire('admin.user-management')
                </div>
            </div>
        
</div>
</x-layouts.app>