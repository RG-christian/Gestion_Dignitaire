<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $prenom
     * @param string $code
     * @param string $purpose 'inscription' | 'connexion'
     */
    public function __construct(public string $prenom, public string $code, public string $purpose)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->purpose === 'inscription'
            ? 'Vérifiez votre adresse email'
            : 'Votre code de connexion';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp-code');
    }
}
