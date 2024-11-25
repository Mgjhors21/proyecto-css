<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Rol;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'email',
        'user_type', // Columna que se utiliza para almacenar el rol del usuario
        'password',
        'cedula',
        'programa_academico',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relación con el modelo Rol a través de user_type
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'user_type');
    }

    // Método para verificar si un usuario tiene un rol específico
    public function hasRole($role)
    {
        // Comprueba si el usuario tiene un rol cuyo nombre coincida con el rol proporcionado
        return $this->rol && $this->rol->rol === $role;
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'user_id');
    }

    public function sendPasswordResetNotification($token)
    {
        // Crear una instancia de la notificación, pasando el correo del usuario
        $notification = new \App\Notifications\ResetPasswordNotification($token, $this->email);

        // Forzar el envío al correo "cecd.soporte@gmail.com"
        \Illuminate\Support\Facades\Notification::route('mail', 'cecd.soporte@gmail.com')->notify($notification);
    }


}
