<?php

namespace App\Mail\Tickets;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketSlaMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  'proximo_a_vencer'|'vencido'  $tipo
     */
    public function __construct(
        public readonly Ticket $ticket,
        public readonly string $tipo,
    ) {}

    public function envelope(): Envelope
    {
        $asunto = $this->tipo === 'vencido'
            ? "SLA vencido — ticket {$this->ticket->ticket_number}"
            : "SLA próximo a vencer — ticket {$this->ticket->ticket_number}";

        return new Envelope(subject: $asunto);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.sla',
            with: ['ticket' => $this->ticket, 'tipo' => $this->tipo],
        );
    }
}
