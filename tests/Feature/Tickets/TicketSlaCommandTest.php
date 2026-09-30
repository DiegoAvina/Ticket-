<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Domain\Tickets\Actions\CheckTicketSlaAction;
use App\Mail\Tickets\TicketSlaMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed();

    Mail::fake();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->servicio = Service::where('department_id', $this->ti->id)->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);

    $this->agente = User::factory()->create(['department_id' => $this->ti->id]);
    $this->agente->assignRole('agente');
});

test('un ticket vencido se notifica una sola vez aunque el job corra varias veces', function () {
    $ticket = app(CreateTicketAction::class)->ejecutar($this->requester, [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket vencido de prueba',
        'description' => 'Descripción.',
    ]);

    $ticket->update(['sla_resolution_due_at' => now()->subHour()]);
    $ticket->assigned_user_id = $this->agente->id;
    $ticket->save();

    $resultado = app(CheckTicketSlaAction::class)->ejecutar();

    expect($resultado['vencidos'])->toBe(1)
        ->and($ticket->refresh()->sla_resolution_breached_at)->not->toBeNull();

    Mail::assertQueued(TicketSlaMailable::class, fn ($mail) => $mail->hasTo($this->agente->email) && $mail->tipo === 'vencido');

    // Segunda corrida: ya no debe volver a notificar el mismo ticket.
    $resultado2 = app(CheckTicketSlaAction::class)->ejecutar();
    expect($resultado2['vencidos'])->toBe(0);
});

test('un ticket próximo a vencer se notifica como aviso previo, no como vencido', function () {
    $ticket = app(CreateTicketAction::class)->ejecutar($this->requester, [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket próximo a vencer',
        'description' => 'Descripción.',
    ]);

    $ticket->update(['sla_resolution_due_at' => now()->addHour()]);
    $ticket->assigned_user_id = $this->agente->id;
    $ticket->save();

    $resultado = app(CheckTicketSlaAction::class)->ejecutar();

    expect($resultado['proximos_a_vencer'])->toBe(1)
        ->and($resultado['vencidos'])->toBe(0);

    Mail::assertQueued(TicketSlaMailable::class, fn ($mail) => $mail->hasTo($this->agente->email) && $mail->tipo === 'proximo_a_vencer');
});

test('un ticket ya resuelto no se toma en cuenta para el SLA', function () {
    $ticket = app(CreateTicketAction::class)->ejecutar($this->requester, [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket resuelto',
        'description' => 'Descripción.',
    ]);

    $ticket->update(['sla_resolution_due_at' => now()->subHour()]);

    $this->actingAs($this->agente)->post(route('tickets.assign', $ticket), ['agente_id' => $this->agente->id]);
    $this->actingAs($this->agente)->post(route('tickets.status', $ticket), ['estado' => 'in_progress']);
    $this->actingAs($this->agente)->post(route('tickets.resolve', $ticket), ['resolucion' => 'Listo.']);

    $resultado = app(CheckTicketSlaAction::class)->ejecutar();

    expect($resultado['vencidos'])->toBe(0)
        ->and($resultado['proximos_a_vencer'])->toBe(0);
});
