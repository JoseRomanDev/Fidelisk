{{-- resources/views/supervisor/panel.blade.php --}}
<x-layouts.app> 
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Panel del Supervisor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <h3 class="text-3xl font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Bienvenido al Panel de Supervisión
                    </h3>
        <div class="mt-6">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                  
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        Monitor de Agentes
                    </h3>
                    
                    
                    <div class="mt-4">
                        @livewire('supervisor.estadisticas-agentes')
                    </div>
                </div>
            </div>
        </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
                <div class="max-w-full">
                    @livewire('supervisor.visor-tickets')
                 </div>
            </div>    
        </div>
    </div>
</x-layouts.app>