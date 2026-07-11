<?php

namespace App\Mail;

use App\Models\Candidat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatRecommandation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidat $candidat, public string $contenu)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle recommandation sur votre candidature');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.candidat-recommandation');
    }
}
