{{-- resources/views/livewire/agente/panel.blade.php --}}
<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel del Agente') }}
        </h2>
    </x-slot>

    {{-- Panel de Llamada Activa --}}
    @livewire('agente.llamada-activa-panel')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-3xl font-medium text-gray-900 dark:text-gray-100 mb-4">
                Bienvenido al Panel del Agente
            </h3>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Tarjeta para Gestión de Clientes --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    @livewire('agente.gestion-clientes')
                </div>
            </div>
            {{-- Tarjeta para Gestión de Tickets --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    @livewire('agente.gestion-tickets')
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>