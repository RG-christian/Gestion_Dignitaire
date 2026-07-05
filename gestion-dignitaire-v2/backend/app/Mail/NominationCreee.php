<?php

namespace App\Mail;

use App\Models\Dignitaire;
use App\Models\Nomination;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NominationCreee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Dignitaire $dignitaire, public Nomination $nomination)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle nomination');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nomination-creee',
            with: [
                'entiteNom' => $this->nomination->entite?->nom,
            ],
        );
    }
}
