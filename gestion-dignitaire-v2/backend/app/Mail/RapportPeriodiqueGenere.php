<?php

namespace App\Mail;

use App\Models\Rapport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RapportPeriodiqueGenere extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Rapport $rapport)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Rapport {$this->rapport->type} disponible");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rapport-periodique',
            with: [
                'rapport' => $this->rapport,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('local', $this->rapport->chemin_fichier)
                ->as($this->rapport->nom_fichier)
                ->withMime('application/pdf'),
        ];
    }
}
