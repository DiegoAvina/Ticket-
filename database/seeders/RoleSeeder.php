<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['usuario', 'agente', 'encargado', 'administrador'] as $rol) {
            Role::firstOrCreate(['name' => $rol]);
        }
    }
}
