<?php

namespace App\Mail\Tickets;

use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAssignedMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly User $agente,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Se te asignó el ticket {$this->ticket->ticket_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.asignado',
            with: ['ticket' => $this->ticket, 'agente' => $this->agente],
        );
    }
}
