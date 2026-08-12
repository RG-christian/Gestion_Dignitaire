<?php

namespace App\Mail;

use App\Models\Candidat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouvelleCandidatureAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidat $candidat)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Nouvelle candidature — {$this->candidat->prenom} {$this->candidat->nom}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nouvelle-candidature-admin',
            with: [
                'candidat' => $this->candidat,
                'lienCandidature' => config('app.frontend_url') . '/admin/candidatures/' . $this->candidat->id,
            ],
        );
    }
}
