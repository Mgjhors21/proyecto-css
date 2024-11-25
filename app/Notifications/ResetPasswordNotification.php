<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public $token;
    public $userEmail;

    public function __construct($token, $userEmail)
    {
        $this->token = $token;
        $this->userEmail = $userEmail; // Recibe el correo del usuario
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Crear el mensaje del correo
        return (new MailMessage)
            ->subject('Solicitud de Restablecimiento de Contraseña')
            ->line('Un usuario ha solicitado restablecer su contraseña.')
            ->line('Correo del usuario: ' . $this->userEmail) // Usa el correo proporcionado
            ->action('Restablecer Contraseña', url(config('app.url') . route('password.reset', $this->token, false)))
            ->line('Si no realizaste esta solicitud, ignora este mensaje.');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
