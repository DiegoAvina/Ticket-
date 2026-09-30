<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;

function mockearCuentaMicrosoft(string $id, string $email, string $nombre): void
{
    $cuenta = Mockery::mock(SocialiteUserContract::class);
    $cuenta->shouldReceive('getId')->andReturn($id);
    $cuenta->shouldReceive('getEmail')->andReturn($email);
    $cuenta->shouldReceive('getName')->andReturn($nombre);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($cuenta);

    Socialite::shouldReceive('driver')->with('microsoft')->andReturn($provider);
}

test('un usuario nuevo que entra con Microsoft se crea con rol usuario y sin departamento', function () {
    $this->seed();

    mockearCuentaMicrosoft('ms-123', 'nueva.persona@dasavena.com', 'Nueva Persona');

    $this->get(route('auth.microsoft.callback'))->assertRedirect(route('dashboard'));

    $user = User::where('email', 'nueva.persona@dasavena.com')->firstOrFail();

    expect($user->microsoft_id)->toBe('ms-123')
        ->and($user->department_id)->toBeNull()
        ->and($user->hasRole('usuario'))->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

test('un usuario existente conserva su rol y departamento al entrar con Microsoft', function () {
    $this->seed();

    $ti = App\Domain\Departments\Models\Department::where('slug', 'ti')->firstOrFail();
    $encargado = User::factory()->create(['email' => 'encargado.existente@dasavena.com', 'department_id' => $ti->id]);
    $encargado->assignRole('encargado');

    mockearCuentaMicrosoft('ms-456', 'encargado.existente@dasavena.com', 'Encargado Existente');

    $this->get(route('auth.microsoft.callback'))->assertRedirect(route('dashboard'));

    $encargado->refresh();

    expect($encargado->microsoft_id)->toBe('ms-456')
        ->and($encargado->department_id)->toBe($ti->id)
        ->and($encargado->hasRole('encargado'))->toBeTrue();
});
