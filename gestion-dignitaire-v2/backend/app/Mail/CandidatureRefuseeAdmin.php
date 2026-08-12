<?php

namespace App\Mail;

use App\Models\Candidat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureRefuseeAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidat $candidat, public string $motif)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Candidature refusée — {$this->candidat->prenom} {$this->candidat->nom}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidature-refusee-admin',
            with: [
                'candidat' => $this->candidat,
                'motif' => $this->motif,
            ],
        );
    }
}
