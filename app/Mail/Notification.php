<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->view('secretaria.respuesta_certificado') // Asegúrate de que esta vista exista
                    ->subject('Carta Repuesta Certificado')
                    ->attach($this->filePath); // Aquí se adjunta el archivo
    }
}
