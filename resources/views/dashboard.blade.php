<x-layouts.app :title="__('FIDELISK')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(auth()->user()->hasRole('agente'))
                    <a href="{{ route('agente.panel') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-blue-50 dark:hover:bg-blue-900">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">Panel de Agente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión de clientes y tickets como agente.</p>
                    </a>
                @endif
                @if(auth()->user()->hasRole('supervisor'))
                    <a href="{{ route('supervisor.panel') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-green-50 dark:hover:bg-green-900">
                        <h3 class="text-lg font-bold text-green-700 dark:text-green-300">Panel de Supervisor</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la supervisión y estadísticas globales.</p>
                    </a>
                    <a href="{{ route('agente.panel') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-blue-50 dark:hover:bg-blue-900">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">Panel de Agente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión de clientes y tickets como agente.</p>
                    </a>
                @endif
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.panel') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-yellow-50 dark:hover:bg-yellow-900">
                        <h3 class="text-lg font-bold text-yellow-700 dark:text-yellow-300">Panel de Administrador</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión completa del sistema.</p>
                    </a>
                    <a href="{{ route('supervisor.panel') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-green-50 dark:hover:bg-green-900">
                        <h3 class="text-lg font-bold text-green-700 dark:text-green-300">Panel de Supervisor</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la supervisión y estadísticas globales.</p>
                    </a>
                    <a href="{{ route('agente.panel') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-blue-50 dark:hover:bg-blue-900">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">Panel de Agente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión de clientes y tickets como agente.</p>
                    </a>
                @endif
                {{-- Puedes agregar más paneles según los roles // necesidades que existan --}}
            </div>
        </div>
    </div>
</x-layouts.app>