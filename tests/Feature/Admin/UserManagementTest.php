<?php

use App\Domain\Departments\Models\Department;
use App\Models\User;

beforeEach(function () {
    $this->seed();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->rh = Department::where('slug', 'rh')->firstOrFail();

    $this->admin = User::factory()->create(['department_id' => $this->ti->id]);
    $this->admin->assignRole('administrador');
});

test('un administrador puede cambiar el rol y el área de un usuario', function () {
    $usuario = User::factory()->create(['department_id' => $this->ti->id]);
    $usuario->assignRole('usuario');

    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $usuario), [
            'roles' => ['encargado'],
            'department_id' => $this->rh->id,
            'active' => '1',
        ])
        ->assertRedirect(route('admin.users.index'));

    $usuario->refresh();

    expect($usuario->hasRole('encargado'))->toBeTrue()
        ->and($usuario->department_id)->toBe($this->rh->id);
});

test('un administrador puede asignarle varios roles a la vez a un usuario', function () {
    $usuario = User::factory()->create(['department_id' => $this->ti->id]);
    $usuario->assignRole('usuario');

    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $usuario), [
            'roles' => ['agente', 'encargado'],
            'department_id' => $this->ti->id,
            'active' => '1',
        ])
        ->assertRedirect(route('admin.users.index'));

    $usuario->refresh();

    expect($usuario->hasRole('agente'))->toBeTrue()
        ->and($usuario->hasRole('encargado'))->toBeTrue()
        ->and($usuario->hasRole('usuario'))->toBeFalse()
        ->and($usuario->roles)->toHaveCount(2);
});

test('un usuario que no es administrador no puede acceder al panel de usuarios', function () {
    $agente = User::factory()->create(['department_id' => $this->ti->id]);
    $agente->assignRole('agente');

    $this->actingAs($agente)->get(route('admin.users.index'))->assertForbidden();
});

test('un administrador no puede quitarse a sí mismo el rol de administrador', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $this->admin), [
            'roles' => ['usuario'],
            'department_id' => $this->ti->id,
            'active' => '1',
        ])
        ->assertSessionHasErrors('roles');

    expect($this->admin->fresh()->hasRole('administrador'))->toBeTrue();
});

test('un usuario desactivado no puede iniciar sesión localmente', function () {
    $usuario = User::factory()->create(['password' => bcrypt('password'), 'active' => false]);

    $this->post(route('login'), [
        'email' => $usuario->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});
