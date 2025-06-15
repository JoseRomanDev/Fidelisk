<div>
    <div class="flex flex-col items-center justify-center min-h-screen bg-white dark:bg-gray-900 px-4 sm:px-6 lg:px-8">

        <div class="w-full max-w-md space-y-8">
            {{-- Contenedor del Logo y Branding --}}
            <div class="text-center">
                <a href="/" wire:navigate class="inline-block mb-4">
                    {{-- Logo --}}
                    <img src="{{ asset('images/logo.png') }}" 
                        alt="FIDELISK Logo" 
                        class="w-70 h-50 mx-auto">
                </a>
                <h1 class="text-5xl font-black text-gray-900 dark:text-gray-100 tracking-wider"style="font-family: 'MPLUSRounded1c', sans-serif;">
                    FIDELISK
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Tu solución de Call Center inteligente y centralizada.</p>
            </div>

            {{-- Tarjeta del Formulario de Login --}}
            <div class="bg-white dark:bg-gray-800/50 p-6 sm:p-8 rounded-lg shadow-md">
                {{-- <x-auth-header> podría ir aquí si la quisieras dentro de la tarjeta --}}
                {{-- Mensaje de estado de la sesión, usando el componente x-auth-session-status --}}
                <x-auth-session-status class="text-center mb-4" :status="session('status')" />

                {{-- FORMULARIO CON LIVEWIRE Y COMPONENTES FLUX --}}
                {{-- wire:submit="login" es esencial para que Livewire maneje el envío --}}
                <form wire:submit="login" class="space-y-6">
                    <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-gray-100">
                        {{-- Puedes usar el componente x-auth-header aquí o un título directo --}}
                        {{ __('Iniciar sesión') }}
                    </h2>

                    {{-- Email usando flux:input --}}
                    <flux:input
                        wire:model="email"
                        :label="__('Email address')" {{-- Utiliza la etiqueta del componente flux --}}
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        {{-- Los errores son manejados por flux:input si está bien implementado --}}
                    />

                    {{-- Contraseña usando flux:input --}}
                    <div class="relative">
                        <flux:input
                            wire:model="password"
                            :label="__('Password')" {{-- Utiliza la etiqueta del componente flux --}}
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('Password')"
                            viewable
                            {{-- Los errores son manejados por flux:input si está bien implementado --}}
                        />

                        {{-- Enlace "¿Olvidaste tu contraseña?" --}}
                        @if (Route::has('password.request'))
                            <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                                {{ __('Forgot your password?') }}
                            </flux:link>
                        @endif
                    </div>

                    {{-- Checkbox "Recordarme" usando flux:checkbox --}}
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="remember" class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                    </label>

                    <div>
                        {{-- Botón de envío usando flux:button --}}
                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('Log in') }} {{-- Texto del botón --}}
                        </flux:button>
                    </div>
                </form>
            </div>

            {{-- Sección para registrarse --}}
            @if (Route::has('register'))
                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Don\'t have an account?') }}
                    <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
                </div>
            @endif
        </div>
    </div>
</div>
