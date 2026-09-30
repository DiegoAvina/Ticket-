<?php

namespace App\Mail\Tickets;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketWaitingUserMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Se requiere tu respuesta en el ticket {$this->ticket->ticket_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.esperando_usuario',
            with: ['ticket' => $this->ticket],
        );
    }
}
