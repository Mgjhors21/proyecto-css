<?php

namespace App\Proxies;

use App\Models\User;

class Userproxy
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getFullName()
    {
        return $this->user->name . ' ' . $this->user->last_name;
    }

    public function getSensitiveInfo($role)
    {
        if ($this->user->hasRole($role)) {
            return [
                'phone' => $this->user->phone,
                'cedula' => $this->user->cedula,
                'programa_academico' => $this->user->programa_academico,
            ];
        }

        return null;
    }

    public function hasRole($role)
    {
        return $this->user->hasRole($role);
    }

    // Método para eliminar el usuario
    public function delete()
    {
        return $this->user->delete();
    }
}
