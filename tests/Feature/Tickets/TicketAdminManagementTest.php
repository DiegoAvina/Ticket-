<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $servicio = Service::where('department_id', $this->ti->id)->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);

    $this->ticket = app(CreateTicketAction::class)->ejecutar($this->requester, [
        'service_id' => $servicio->id,
        'title' => 'Título original',
        'description' => 'Descripción original.',
    ]);

    $this->admin = User::factory()->create(['department_id' => $this->ti->id]);
    $this->admin->assignRole('administrador');

    $this->agenteOtraArea = User::factory()->create([
        'department_id' => Department::where('slug', 'rh')->firstOrFail()->id,
    ]);
    $this->agenteOtraArea->assignRole('agente');
});

test('un administrador puede editar el título y la prioridad de cualquier ticket', function () {
    $this->actingAs($this->admin)->patch(route('tickets.update', $this->ticket), [
        'title' => 'Título corregido por admin',
        'description' => $this->ticket->description,
        'priority_id' => $this->ticket->priority_id,
    ])->assertRedirect();

    expect($this->ticket->fresh()->title)->toBe('Título corregido por admin')
        ->and($this->ticket->history()->where('action', 'editado_por_admin')->exists())->toBeTrue();
});

test('un administrador puede eliminar cualquier ticket', function () {
    $this->actingAs($this->admin)->delete(route('tickets.destroy', $this->ticket))
        ->assertRedirect(route('tickets.index'));

    expect(Ticket::find($this->ticket->id))->toBeNull();
});

test('un agente de otra área no puede editar ni eliminar el ticket', function () {
    $this->actingAs($this->agenteOtraArea)
        ->patch(route('tickets.update', $this->ticket), [
            'title' => 'Intento no autorizado',
            'description' => $this->ticket->description,
            'priority_id' => $this->ticket->priority_id,
        ])->assertForbidden();

    $this->actingAs($this->agenteOtraArea)
        ->delete(route('tickets.destroy', $this->ticket))
        ->assertForbidden();

    expect(Ticket::find($this->ticket->id))->not->toBeNull();
});
