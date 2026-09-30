<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Priority;
use App\Domain\SLA\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    /**
     * Una política 1:1 por prioridad, con los mismos minutos de la sección 14.
     */
    public function run(): void
    {
        Priority::all()->each(function (Priority $prioridad) {
            SlaPolicy::firstOrCreate(
                ['name' => "SLA {$prioridad->name}"],
                [
                    'priority_id' => $prioridad->id,
                    'first_response_minutes' => $prioridad->first_response_minutes,
                    'resolution_minutes' => $prioridad->resolution_minutes,
                    'business_hours_aware' => false,
                ],
            );
        });
    }
}
