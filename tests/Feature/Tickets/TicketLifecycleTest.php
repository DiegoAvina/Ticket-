<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->servicio = Service::where('department_id', $this->ti->id)->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);

    $this->agente = User::factory()->create(['department_id' => $this->ti->id]);
    $this->agente->assignRole('agente');
});

test('ciclo de vida completo: crear, asignar, comentar, resolver, reabrir, cerrar', function () {
    // 1. Crear
    $this->actingAs($this->requester)
        ->post(route('tickets.store'), [
            'service_id' => $this->servicio->id,
            'title' => 'La impresora no imprime',
            'description' => 'La impresora del segundo piso no responde.',
        ])
        ->assertRedirect();

    $ticket = Ticket::firstOrFail();

    expect($ticket->status->code)->toBe('new')
        ->and($ticket->ticket_number)->toStartWith('TKT-'.now()->year.'-')
        ->and($ticket->assigned_department_id)->toBe($this->ti->id);

    // 2. Asignar
    $this->actingAs($this->agente)
        ->post(route('tickets.assign', $ticket), ['agente_id' => $this->agente->id])
        ->assertRedirect();

    $ticket->refresh();
    expect($ticket->status->code)->toBe('assigned')
        ->and($ticket->assigned_user_id)->toBe($this->agente->id);

    // 3. Iniciar atención
    $this->actingAs($this->agente)
        ->post(route('tickets.status', $ticket), ['estado' => 'in_progress'])
        ->assertRedirect();

    expect($ticket->refresh()->status->code)->toBe('in_progress');

    // 4. Comentar (primera respuesta del agente)
    $this->actingAs($this->agente)
        ->post(route('tickets.messages', $ticket), ['body' => 'Estamos revisando el equipo.'])
        ->assertRedirect();

    $ticket->refresh();
    expect($ticket->first_response_at)->not->toBeNull()
        ->and($ticket->messages()->count())->toBe(1);

    // 5. Resolver
    $this->actingAs($this->agente)
        ->post(route('tickets.resolve', $ticket), ['resolucion' => 'Se reemplazó el cartucho de tóner.'])
        ->assertRedirect();

    $ticket->refresh();
    expect($ticket->status->code)->toBe('resolved')
        ->and($ticket->resolved_at)->not->toBeNull();

    // 6. Reabrir (el problema continúa)
    $this->actingAs($this->requester)
        ->post(route('tickets.reopen', $ticket), ['motivo' => 'Sigue sin imprimir a color.'])
        ->assertRedirect();

    $ticket->refresh();
    expect($ticket->status->code)->toBe('in_progress')
        ->and($ticket->resolved_at)->toBeNull();

    // 7. Resolver de nuevo y cerrar
    $this->actingAs($this->agente)
        ->post(route('tickets.resolve', $ticket), ['resolucion' => 'Se sustituyó el cartucho de color.'])
        ->assertRedirect();

    $this->actingAs($this->requester)
        ->post(route('tickets.close', $ticket))
        ->assertRedirect();

    $ticket->refresh();
    expect($ticket->status->code)->toBe('closed')
        ->and($ticket->closed_at)->not->toBeNull();

    // El historial registró cada paso.
    expect($ticket->history()->count())->toBeGreaterThanOrEqual(7);
});

test('no se puede saltar directamente de nuevo a resuelto', function () {
    $this->actingAs($this->requester)
        ->post(route('tickets.store'), [
            'service_id' => $this->servicio->id,
            'title' => 'Ticket de prueba',
            'description' => 'Descripción de prueba.',
        ]);

    $ticket = Ticket::firstOrFail();

    $this->actingAs($this->agente)
        ->post(route('tickets.resolve', $ticket), ['resolucion' => 'Intento inválido'])
        ->assertSessionHasErrors('estado');

    expect($ticket->refresh()->status->code)->toBe('new');
});
