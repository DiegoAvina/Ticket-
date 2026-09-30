<?php

namespace Database\Seeders;

use App\Domain\Departments\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['TI', 'RH', 'Mantenimiento', 'Compras', 'Finanzas', 'Producción'] as $nombre) {
            Department::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                ['name' => $nombre, 'active' => true],
            );
        }
    }
}
