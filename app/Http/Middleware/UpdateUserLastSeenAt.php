<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeenAt
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Actualiza solo si ha pasado un poco de tiempo para no sobrecargar la BD en cada request
            // Por ejemplo, actualiza si han pasado más de 60 segundos desde la última vez.
            // O, para empezar, puedes actualizarlo siempre.
            if ($user->last_seen_at === null || $user->last_seen_at->diffInMinutes(now()) > 1) {
                 $user->last_seen_at = now();
                 $user->saveQuietly(); // saveQuietly para no disparar eventos de Eloquent si no es necesario
            }
        }
        return $next($request);
    }
}
#Nota: He añadido una condición para actualizar `last_seen_at` solo si ha pasado más de un minuto para optimizar un poco, pero puedes quitarla si prefieres actualizar en cada petición. `saveQuietly()` evita disparar eventos del modelo, lo cual suele ser bueno para este tipo de actualizaciones frecuentes y silenciosas).
