<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Sección 14 del spec: minutos de primera respuesta / resolución por prioridad.
     */
    public function run(): void
    {
        $prioridades = [
            ['name' => 'Baja', 'level' => 1, 'first_response_minutes' => 8 * 60, 'resolution_minutes' => 48 * 60],
            ['name' => 'Media', 'level' => 2, 'first_response_minutes' => 2 * 60, 'resolution_minutes' => 8 * 60],
            ['name' => 'Alta', 'level' => 3, 'first_response_minutes' => 30, 'resolution_minutes' => 4 * 60],
            ['name' => 'Crítica', 'level' => 4, 'first_response_minutes' => 15, 'resolution_minutes' => 2 * 60],
        ];

        foreach ($prioridades as $prioridad) {
            Priority::firstOrCreate(['name' => $prioridad['name']], $prioridad);
        }
    }
}
