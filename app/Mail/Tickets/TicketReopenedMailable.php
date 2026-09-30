<?php

namespace App\Mail\Tickets;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReopenedMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly ?string $motivo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "El ticket {$this->ticket->ticket_number} fue reabierto",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.reabierto',
            with: ['ticket' => $this->ticket, 'motivo' => $this->motivo],
        );
    }
}
