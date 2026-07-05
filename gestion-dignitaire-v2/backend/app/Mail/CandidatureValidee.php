<?php

namespace App\Mail;

use App\Models\Candidat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureValidee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidat $candidat)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre candidature a été validée');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.candidature-validee');
    }
}
