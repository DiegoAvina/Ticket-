<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\SLA\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Catálogo de ejemplo (sección 11 del spec): TI, RH y Mantenimiento con
     * los servicios que el propio spec detalla; el resto de departamentos
     * con un servicio genérico para poder probar el flujo completo.
     */
    public function run(): void
    {
        $porDepartamento = [
            'TI' => ['Hardware', 'Software', 'Microsoft 365', 'Redes', 'Impresoras', 'Telefonía', 'Sistemas'],
            'RH' => ['Vacaciones', 'Constancias', 'Nómina', 'Altas', 'Bajas'],
            'Mantenimiento' => ['Electricidad', 'Plomería', 'Aire acondicionado', 'Infraestructura', 'Equipos'],
            'Compras' => ['Solicitud de compra'],
            'Finanzas' => ['Solicitud de pago'],
            'Producción' => ['Reporte de incidencia en línea'],
        ];

        $prioridadMedia = Priority::where('name', 'Media')->first();
        $slaMedia = SlaPolicy::where('priority_id', $prioridadMedia?->id)->first();

        foreach ($porDepartamento as $nombreDepartamento => $servicios) {
            $departamento = Department::where('name', $nombreDepartamento)->first();

            if (! $departamento) {
                continue;
            }

            $categoria = Category::where('department_id', $departamento->id)->first();

            foreach ($servicios as $nombreServicio) {
                Service::firstOrCreate(
                    ['department_id' => $departamento->id, 'name' => $nombreServicio],
                    [
                        'category_id' => $categoria?->id,
                        'default_priority_id' => $prioridadMedia?->id,
                        'sla_policy_id' => $slaMedia?->id,
                        'active' => true,
                    ],
                );
            }
        }

        $this->crearServicioChatbotSgi($prioridadMedia, $slaMedia);
    }

    /**
     * Servicio destino de los tickets que llegan del chatbot de soporte de
     * sgiDasavena (ver SgiDasavenaTicketController) — vive dentro de TI
     * porque el chatbot es de soporte técnico ("itSupportChat").
     */
    private function crearServicioChatbotSgi(?Priority $prioridadMedia, ?SlaPolicy $slaMedia): void
    {
        $ti = Department::where('slug', 'ti')->first();

        if (! $ti) {
            return;
        }

        $categoriaGeneral = Category::where('department_id', $ti->id)->where('name', 'General')->first();

        Service::firstOrCreate(
            ['department_id' => $ti->id, 'name' => 'Chatbot SGI'],
            [
                'category_id' => $categoriaGeneral?->id,
                'default_priority_id' => $prioridadMedia?->id,
                'sla_policy_id' => $slaMedia?->id,
                'active' => true,
            ],
        );
    }
}
