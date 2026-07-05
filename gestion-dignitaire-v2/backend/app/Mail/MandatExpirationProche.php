<?php

namespace App\Mail;

use App\Models\Dignitaire;
use App\Models\Nomination;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MandatExpirationProche extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Dignitaire $dignitaire, public Nomination $nomination)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre mandat arrive à échéance');
    }

    public function content(): Content
    {
        $joursRestants = now()->startOfDay()->diffInDays(
            \Carbon\Carbon::parse($this->nomination->date_fin)->startOfDay(),
            false
        );

        return new Content(
            view: 'emails.mandat-expiration-proche',
            with: [
                'joursRestants' => max(0, (int) $joursRestants),
            ],
        );
    }
}
