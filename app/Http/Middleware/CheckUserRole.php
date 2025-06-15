<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): 
     * @param  string  ...$roles  Los roles permitidos para acceder a la ruta.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) { // Si no está autenticado, redirige al login
            return redirect('login');
        }

        $user = Auth::user();
        // Asumimos que tienes una relación 'roles' en tu modelo User
        // y que cada rol tiene un atributo 'name' (ej. 'agente', 'supervisor').
        if (!$user->roles()->whereIn('name', $roles)->exists()) {
            // Si el usuario no tiene ninguno de los roles permitidos, aborta.
            abort(403, 'Acceso no autorizado.');
        }
        return $next($request);
    }
}