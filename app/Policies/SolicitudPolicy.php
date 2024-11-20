<?php

namespace App\Policies;

use App\Models\User;

class SolicitudPolicy
{
    /**
     * Create a new policy instance.
     */
    public function verListaSolicitudes(User $user)
    {
        return $user->role === 'coordinador';
    }

    public function verFormularioCertificado(User $user)
    {
        return $user->role === 'usuario';
    }

}
