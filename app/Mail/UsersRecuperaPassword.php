<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsersRecuperaPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected array $with)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recupera Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.users-recupera-password',
            with: $this->with,
        );
    }
}
