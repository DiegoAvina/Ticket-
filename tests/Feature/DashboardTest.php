<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $ti = Department::where('slug', 'ti')->firstOrFail();
    $servicio = Service::where('department_id', $ti->id)->firstOrFail();
    $requester = User::where('email', 'usuario@mesa-ayuda.test')->firstOrFail();

    app(CreateTicketAction::class)->ejecutar($requester, [
        'service_id' => $servicio->id,
        'title' => 'Ticket de prueba para el dashboard',
        'description' => 'Descripción de prueba.',
    ]);
});

test('el usuario ve su dashboard con sus propios tickets', function () {
    $this->actingAs(User::where('email', 'usuario@mesa-ayuda.test')->firstOrFail())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboards.usuario')
        ->assertSee('Ticket de prueba para el dashboard');
});

test('el agente ve su dashboard con los tickets nuevos de su área', function () {
    $this->actingAs(User::where('email', 'agente@mesa-ayuda.test')->firstOrFail())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboards.agente')
        ->assertViewHas('nuevosDelArea', 1);
});

test('el encargado ve el dashboard de su departamento', function () {
    $this->actingAs(User::where('email', 'encargado@mesa-ayuda.test')->firstOrFail())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboards.encargado')
        ->assertViewHas('nuevos', 1);
});

test('el encargado de RH no ve el ticket creado en TI', function () {
    $this->actingAs(User::where('email', 'encargado.rh@mesa-ayuda.test')->firstOrFail())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboards.encargado')
        ->assertViewHas('nuevos', 0);
});

test('el administrador ve el dashboard global', function () {
    $this->actingAs(User::where('email', 'administrador@mesa-ayuda.test')->firstOrFail())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboards.administrador')
        ->assertViewHas('total', 1);
});
