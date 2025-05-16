<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class MfaCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $user;
    public $expiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $code, \DateTime $expiresAt)
    {
        $this->user = $user;
        $this->code = $code;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre code d\'authentification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.auth.mfa-code',
            with: [
                'code' => $this->code,
                'name' => $this->user->name,
                'expiresAt' => $this->expiresAt->format('H:i'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
