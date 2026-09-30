<?php

use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();

    $this->admin = User::factory()->create(['department_id' => $this->ti->id]);
    $this->admin->assignRole('administrador');
});

test('un administrador puede crear, editar y eliminar un servicio', function () {
    $respuesta = $this->actingAs($this->admin)->post(route('admin.services.store'), [
        'department_id' => $this->ti->id,
        'name' => 'Servicio de prueba',
    ]);
    $respuesta->assertRedirect(route('admin.services.index'));

    $servicio = Service::where('name', 'Servicio de prueba')->firstOrFail();

    $this->actingAs($this->admin)->put(route('admin.services.update', $servicio), [
        'department_id' => $this->ti->id,
        'name' => 'Servicio de prueba (editado)',
        'active' => '1',
    ])->assertRedirect(route('admin.services.index'));

    expect($servicio->fresh()->name)->toBe('Servicio de prueba (editado)');

    $this->actingAs($this->admin)->delete(route('admin.services.destroy', $servicio))
        ->assertRedirect(route('admin.services.index'));

    expect(Service::find($servicio->id))->toBeNull();
});

test('no se puede eliminar una prioridad que ya tiene tickets asociados', function () {
    $servicio = Service::where('department_id', $this->ti->id)->firstOrFail();
    $requester = User::factory()->create(['department_id' => $this->ti->id]);

    $ticket = app(CreateTicketAction::class)->ejecutar($requester, [
        'service_id' => $servicio->id,
        'title' => 'Ticket de prueba',
        'description' => 'Descripción.',
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.priorities.destroy', $ticket->priority_id))
        ->assertSessionHasErrors('priority');

    expect(Priority::find($ticket->priority_id))->not->toBeNull();
});

test('un usuario que no es administrador no puede acceder al catálogo', function () {
    $agente = User::factory()->create(['department_id' => $this->ti->id]);
    $agente->assignRole('agente');

    $this->actingAs($agente)->get(route('admin.departments.index'))->assertForbidden();
});
