<?php

namespace App\Support\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Busca un usuario por correo (o por microsoft_id) y, si no existe, lo crea
 * como cuenta "usuario" sin departamento — misma regla que ya usaba
 * MicrosoftLoginController para altas automáticas por login corporativo, y
 * que ahora también necesitan los tickets que llegan de sistemas externos.
 */
class FindOrCreateUserByEmail
{
    public function resolver(string $email, string $name, ?string $microsoftId = null): User
    {
        $user = User::where('email', $email)
            ->when($microsoftId, fn ($query) => $query->orWhere('microsoft_id', $microsoftId))
            ->first();

        if ($user) {
            $user->microsoft_id ??= $microsoftId;
            $user->email_verified_at ??= now();
            $user->save();

            return $user;
        }

        $user = User::create([
            'name' => $name ?: $email,
            'email' => $email,
            'microsoft_id' => $microsoftId,
            // Contraseña aleatoria e inutilizable: esta cuenta nunca inicia
            // sesión con contraseña local.
            'password' => Hash::make(Str::random(40)),
            'email_verified_at' => now(),
            'active' => true,
        ]);

        $user->assignRole('usuario');

        return $user;
    }
}
