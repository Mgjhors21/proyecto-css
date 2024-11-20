<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->roles->rol === 'admin'; // Permitir solo a los administradores
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model)
    {
        return $user->id === $model->id || $user->roles->rol === 'admin';
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
    public function update(User $user, User $model)
    {
        return $user->id === $model->id || $user->roles->rol === 'admin';
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        return $user->roles->rol === 'admin';
    }
}
