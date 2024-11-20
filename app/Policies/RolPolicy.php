<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->roles->rol === 'admin'; // Solo administradores pueden ver roles
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Rol $rol)
    {
        return $user->roles->rol === 'admin';
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user)
    {
        return $user->roles->rol === 'admin';
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, Rol $rol)
    {
        return $user->roles->rol === 'admin';
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Rol $rol)
    {
        return $user->roles->rol === 'admin';
    }
}
