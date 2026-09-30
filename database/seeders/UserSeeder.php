<?php

namespace Database\Seeders;

use App\Domain\Departments\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Crea un usuario de prueba por rol para poder probar manualmente el
     * flujo de autenticación, permisos y aislamiento entre departamentos.
     * Los agentes/encargados de TI y de RH permiten verificar en el
     * navegador que un ticket de un área no es visible desde la otra.
     */
    public function run(): void
    {
        $ti = Department::where('slug', 'ti')->first();
        $rh = Department::where('slug', 'rh')->first();

        // Administrador real (no es un usuario de prueba): se crea con rol
        // administrador desde ya para que, cuando entre con "Iniciar sesión
        // con Microsoft" usando este mismo correo, MicrosoftLoginController
        // lo encuentre por email y le respete el rol en vez de crearlo como
        // "usuario" nuevo. La contraseña local es aleatoria e inutilizable
        // porque el acceso real será por Microsoft.
        $admin = User::firstOrCreate(
            ['email' => 'aux.sistemas@dasavena.com'],
            [
                'name' => 'Auxiliar de Sistemas',
                'password' => Hash::make(Str::random(40)),
                'department_id' => $ti?->id,
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles(['administrador']);

        // Si la cuenta ya existía (p. ej. auto-registrada antes de que
        // existiera este seeder) y quedó sin verificar, se verifica ahora:
        // es el administrador real, no debería quedar bloqueado por el
        // middleware "verified" en /dashboard.
        if (! $admin->email_verified_at) {
            $admin->forceFill(['email_verified_at' => now()])->save();
        }

        $usuarios = [
            ['email' => 'usuario@mesa-ayuda.test', 'name' => 'Usuario Prueba', 'rol' => 'usuario', 'departamento' => $ti],
            ['email' => 'agente@mesa-ayuda.test', 'name' => 'Agente Prueba (TI)', 'rol' => 'agente', 'departamento' => $ti],
            ['email' => 'encargado@mesa-ayuda.test', 'name' => 'Encargado Prueba (TI)', 'rol' => 'encargado', 'departamento' => $ti],
            ['email' => 'administrador@mesa-ayuda.test', 'name' => 'Administrador Prueba', 'rol' => 'administrador', 'departamento' => $ti],
            ['email' => 'agente.rh@mesa-ayuda.test', 'name' => 'Agente Prueba (RH)', 'rol' => 'agente', 'departamento' => $rh],
            ['email' => 'encargado.rh@mesa-ayuda.test', 'name' => 'Encargado Prueba (RH)', 'rol' => 'encargado', 'departamento' => $rh],
        ];

        foreach ($usuarios as $datos) {
            $user = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => 'password',
                    'department_id' => $datos['departamento']?->id,
                    'email_verified_at' => now(),
                ],
            );

            $user->syncRoles([$datos['rol']]);
        }
    }
}
