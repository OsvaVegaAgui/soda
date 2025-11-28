<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    /**
     * Recibe la URL desde el controlador.
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Asunto del correo.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablecimiento de contraseña | Soda IACSA'
        );
    }

    /**
     * Contenido del correo + pasar variable a la vista.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_code',
            with: ['url' => $this->url] 
        );
    }

    /**
     * Adjuntos (opcional).
     */
    public function attachments(): array
    {
        return [];
    }
}
