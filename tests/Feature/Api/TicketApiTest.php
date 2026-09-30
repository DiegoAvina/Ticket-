<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Actions\ChangeTicketStatusAction;
use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->rh = Department::where('slug', 'rh')->firstOrFail();
    $this->servicio = Service::where('department_id', $this->ti->id)->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);

    $this->agenteTi = User::factory()->create(['department_id' => $this->ti->id]);
    $this->agenteTi->assignRole('agente');

    $this->agenteRh = User::factory()->create(['department_id' => $this->rh->id]);
    $this->agenteRh->assignRole('agente');
});

test('crear, asignar y resolver un ticket vía API', function () {
    Sanctum::actingAs($this->requester);

    $respuesta = $this->postJson('/api/v1/tickets', [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket vía API',
        'description' => 'Descripción de prueba.',
    ]);

    $respuesta->assertCreated()->assertJsonPath('data.status.code', 'new');
    $ticketId = $respuesta->json('data.id');

    Sanctum::actingAs($this->agenteTi);

    $this->postJson("/api/v1/tickets/{$ticketId}/assign", ['agente_id' => $this->agenteTi->id])
        ->assertOk()
        ->assertJsonPath('data.status.code', 'assigned');

    // La API no expone un endpoint genérico de cambio de estado (sección 27
    // del spec no lo pide); se avanza a "in_progress" con la Action interna
    // para poder probar el endpoint de "resolve" bajo prueba.
    app(ChangeTicketStatusAction::class)->ejecutar(
        Ticket::findOrFail($ticketId),
        TicketStatusCode::IN_PROGRESS,
        $this->agenteTi,
    );

    $this->postJson("/api/v1/tickets/{$ticketId}/resolve", ['resolucion' => 'Resuelto vía API.'])
        ->assertOk()
        ->assertJsonPath('data.status.code', 'resolved');
});

test('la API respeta el aislamiento por departamento igual que la web', function () {
    Sanctum::actingAs($this->requester);
    $respuesta = $this->postJson('/api/v1/tickets', [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket privado de TI',
        'description' => 'Descripción.',
    ]);
    $ticketId = $respuesta->json('data.id');

    Sanctum::actingAs($this->agenteRh);
    $this->getJson("/api/v1/tickets/{$ticketId}")->assertForbidden();

    $listado = $this->getJson('/api/v1/tickets')->assertOk();
    expect(collect($listado->json('data'))->pluck('id'))->not->toContain($ticketId);
});

test('sin token no se puede acceder a la API', function () {
    $this->getJson('/api/v1/tickets')->assertUnauthorized();
});
