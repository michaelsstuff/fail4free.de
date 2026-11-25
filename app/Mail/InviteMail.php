<?php

namespace App\Mail;

use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $acceptUrl;

    public function __construct(
        public Invite $invite,
        string $plainToken,
    ) {
        $this->acceptUrl = route('invites.accept.show', ['token' => $plainToken]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Einladung zu fail4free.de',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invite',
            text: 'emails.invite-text',
        );
    }
}
