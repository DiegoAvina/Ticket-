<?php

namespace App\Mail\Tickets;

use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMessageAddedMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketMessage $mensaje,
        public readonly User $destinatario,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nuevo comentario en el ticket {$this->ticket->ticket_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.mensaje',
            with: ['ticket' => $this->ticket, 'mensaje' => $this->mensaje, 'destinatario' => $this->destinatario],
        );
    }
}
