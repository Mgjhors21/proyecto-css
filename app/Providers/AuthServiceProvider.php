<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Solicitud_extencion;
use App\Policies\SolicitudPolicy;
use App\Models\User;
use App\Models\Rol;
use App\Policies\UserPolicy;
use App\Policies\RolPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * El array de políticas del modelo.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Solicitud_extencion::class => SolicitudPolicy::class, // Relaciona el modelo con la política
        User::class => UserPolicy::class,
        Rol::class => RolPolicy::class,
    ];

    /**
     * Registrar cualquier servicio de autenticación/autorización.
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
