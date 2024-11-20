<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Roles
{
    public function handle($request, Closure $next, $roles)
    {
        $user = Auth::user();

        $user = $request->user(); // Obtener el usuario autenticado

        if (!$user || !$user->roles()->whereIn('rol', $roles)->exists()) {
            abort(403, 'Acceso no autorizado'); // O redirigir, según tu lógica
        }


        return $next($request);
    }
}
