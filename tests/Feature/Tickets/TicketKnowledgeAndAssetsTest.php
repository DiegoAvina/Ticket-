<?php

use App\Domain\Assets\Models\Asset;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->servicioImpresoras = Service::where('name', 'Impresoras')->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);
});

test('el formulario de crear ticket incluye los artículos sugeridos del servicio elegido', function () {
    $respuesta = $this->actingAs($this->requester)->get(route('tickets.create'));

    $respuesta->assertOk()
        ->assertSee('sin papel', false)
        ->assertSee((string) $this->servicioImpresoras->id);
});

test('un ticket se puede crear sin activo asociado', function () {
    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicioImpresoras->id,
        'title' => 'Ticket sin activo',
        'description' => 'Descripción.',
    ])->assertRedirect();

    expect(Ticket::first()->asset_id)->toBeNull();
});

test('un ticket se puede crear asociado a un activo propio', function () {
    $activo = Asset::create([
        'type' => 'laptop',
        'name' => 'Laptop Dell',
        'assigned_user_id' => $this->requester->id,
        'department_id' => $this->ti->id,
    ]);

    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicioImpresoras->id,
        'asset_id' => $activo->id,
        'title' => 'Ticket con activo propio',
        'description' => 'Descripción.',
    ])->assertRedirect();

    expect(Ticket::first()->asset_id)->toBe($activo->id);
});

test('un usuario no puede asociar un activo que no es suyo', function () {
    $otroUsuario = User::factory()->create(['department_id' => $this->ti->id]);

    $activoAjeno = Asset::create([
        'type' => 'laptop',
        'name' => 'Laptop de otra persona',
        'assigned_user_id' => $otroUsuario->id,
        'department_id' => $this->ti->id,
    ]);

    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicioImpresoras->id,
        'asset_id' => $activoAjeno->id,
        'title' => 'Intento de asociar activo ajeno',
        'description' => 'Descripción.',
    ])->assertSessionHasErrors('asset_id');

    expect(Ticket::count())->toBe(0);
});
