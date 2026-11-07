<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FamilyMemberWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $password;
    public $family;

    /**
     * Create a new message instance.
     */


    public function __construct($user, $password, $family)
    {
        $this->user = $user;
        $this->password = $password;
        $this->family = $family;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Family Member Welcome Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Your Family Account Created')
            ->markdown('emails.family.welcome');
    }
    // public function content(): Content
    // {
    //     return new Content(
    //         markdown: 'emails.family.welcome',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
