<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('integrations:sgidasavena:token {--rotate : Revoca el token anterior y emite uno nuevo}')]
#[Description('Crea (si hace falta) la cuenta de servicio de la integración con sgiDasavena y emite su token de API.')]
class CreateSgiDasavenaIntegrationToken extends Command
{
    private const EMAIL = 'integraciones@dasavena.com';

    private const NOMBRE_TOKEN = 'sgidasavena';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $cuenta = User::firstOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'Integración sgiDasavena',
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
                // Nunca debe poder iniciar sesión interactivamente, solo existe
                // para sostener el token de la integración.
                'active' => false,
            ],
        );

        $tokenExistente = $cuenta->tokens()->where('name', self::NOMBRE_TOKEN)->first();

        if ($tokenExistente && ! $this->option('rotate')) {
            $this->error('Ya existe un token activo para esta integración. Usa --rotate para reemplazarlo por uno nuevo.');

            return;
        }

        $tokenExistente?->delete();

        $token = $cuenta->createToken(self::NOMBRE_TOKEN, ['tickets:create'])->plainTextToken;

        $this->info('Token generado (guárdalo ahora, no se puede volver a mostrar):');
        $this->line($token);
        $this->comment('Pégalo en el .env de sgiDasavena como MESA_AYUDA_API_TOKEN.');
    }
}
