<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->rh = Department::where('slug', 'rh')->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);

    $this->agenteTi = User::factory()->create(['department_id' => $this->ti->id]);
    $this->agenteTi->assignRole('agente');

    $this->agenteRh = User::factory()->create(['department_id' => $this->rh->id]);
    $this->agenteRh->assignRole('agente');

    $this->administrador = User::factory()->create(['department_id' => $this->ti->id]);
    $this->administrador->assignRole('administrador');

    $servicioRh = Service::where('department_id', $this->rh->id)->firstOrFail();

    $this->ticket = app(CreateTicketAction::class)->ejecutar($this->requester, [
        'service_id' => $servicioRh->id,
        'title' => 'Solicitud de vacaciones',
        'description' => 'Necesito solicitar mis vacaciones del próximo mes.',
    ]);
});

test('un agente puede ver un ticket de su propio departamento', function () {
    $this->actingAs($this->agenteRh)
        ->get(route('tickets.show', $this->ticket))
        ->assertOk();
});

test('un agente NO puede ver un ticket de otro departamento (IDOR)', function () {
    $this->actingAs($this->agenteTi)
        ->get(route('tickets.show', $this->ticket))
        ->assertForbidden();
});

test('el administrador puede ver tickets de cualquier departamento', function () {
    $this->actingAs($this->administrador)
        ->get(route('tickets.show', $this->ticket))
        ->assertOk();
});

test('el solicitante puede ver su propio ticket aunque sea de otro departamento', function () {
    $this->actingAs($this->requester)
        ->get(route('tickets.show', $this->ticket))
        ->assertOk();
});

test('el listado de tickets solo muestra los del propio departamento', function () {
    $this->actingAs($this->agenteTi)
        ->get(route('tickets.index'))
        ->assertOk()
        ->assertDontSee($this->ticket->ticket_number);

    $this->actingAs($this->agenteRh)
        ->get(route('tickets.index'))
        ->assertOk()
        ->assertSee($this->ticket->ticket_number);
});

test('un agente de otro departamento no puede gestionar el ticket', function () {
    $this->actingAs($this->agenteTi)
        ->post(route('tickets.status', $this->ticket), ['estado' => 'assigned'])
        ->assertForbidden();
});
