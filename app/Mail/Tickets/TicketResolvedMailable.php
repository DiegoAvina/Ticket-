<?php

namespace App\Mail\Tickets;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketResolvedMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu ticket {$this->ticket->ticket_number} fue resuelto",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.resuelto',
            with: ['ticket' => $this->ticket],
        );
    }
}
