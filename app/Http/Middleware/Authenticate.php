<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function authenticated(Request $request, $user)
{
    if ($user->hasRole('adminitrador')) {
        return redirect('/resources/views/template');
    } elseif ($user->hasRole('estudiante')) {
        return redirect('/resources/views/solicitud/principal_form');
    }

    return redirect('/login');
}
}
