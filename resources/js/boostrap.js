import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher; // Reverb usa la API de Pusher, por lo que necesitas el paquete pusher-js
window.Pusher.logToConsole = true;
window.Echo = new Echo({
    broadcaster: 'reverb', // Indica que usarás Reverb
    key: import.meta.env.VITE_REVERB_APP_KEY, // Obtiene la clave de tu .env
    wsHost: import.meta.env.VITE_REVERB_HOST, // Host del servidor Reverb
    wsPort: import.meta.env.VITE_REVERB_PORT, // Puerto de Reverb
    wssPort: import.meta.env.VITE_REVERB_PORT, // Puerto de Reverb si usas SSL/TLS
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https', // Fuerza TLS si el esquema es https
    disableStats: true, // Puedes habilitar esto en producción si lo necesitas
    enabledTransports: ['ws', 'wss'], // Tipos de transporte habilitados
});
