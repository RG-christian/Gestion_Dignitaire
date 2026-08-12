<?php

namespace App\Mail;

use App\Models\Candidat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureValideeAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidat $candidat)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Candidature validée — {$this->candidat->prenom} {$this->candidat->nom}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidature-validee-admin',
            with: [
                'candidat' => $this->candidat,
                'lienDignitaire' => config('app.frontend_url') . '/dignitaires/' . $this->candidat->dignitaire_id,
            ],
        );
    }
}
