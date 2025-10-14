<?php

namespace App\Mail;

use App\Models\CompanyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public CompanyInvitation $invitation;
    public string $plainPassword;
    public string $invitationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(CompanyInvitation $invitation, string $plainPassword, string $invitationUrl)
    {
        $this->invitation = $invitation;
        $this->plainPassword = $plainPassword;
        $this->invitationUrl = $invitationUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Company Manager Invitation - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.company-invitation',
            with: [
                'invitation' => $this->invitation,
                'plainPassword' => $this->plainPassword,
                'invitationUrl' => $this->invitationUrl,
            ],
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
