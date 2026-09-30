<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Service;
use App\Domain\Knowledge\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Artículos de ejemplo (sección 32 del spec) para que las sugerencias
     * al crear un ticket se puedan ver funcionando de inmediato.
     */
    public function run(): void
    {
        $impresoras = Service::where('name', 'Impresoras')->first();
        $redes = Service::where('name', 'Redes')->first();
        $vacaciones = Service::where('name', 'Vacaciones')->first();

        $articulos = [
            [
                'servicio' => $impresoras,
                'title' => 'La impresora marca "sin papel" aunque tiene papel cargado',
                'content' => "1. Abre la bandeja y retira el papel.\n2. Airea el paquete de hojas antes de recargarlo.\n3. Alinea las guías laterales sin apretar de más.\n4. Vuelve a cargar el papel y cierra la bandeja.",
            ],
            [
                'servicio' => $impresoras,
                'title' => 'Cómo instalar una impresora de red en tu equipo',
                'content' => "1. Ve a Configuración → Impresoras y escáneres.\n2. Agregar impresora → selecciona la impresora del área.\n3. Si no aparece, contacta a TI para verificar permisos de red.",
            ],
            [
                'servicio' => $redes,
                'title' => 'No tengo internet / la red está lenta',
                'content' => "1. Reinicia tu equipo y el punto de red si tienes uno propio.\n2. Verifica si el problema es general en tu área (pregunta a un compañero).\n3. Si persiste, crea un ticket indicando desde cuándo y si es constante o intermitente.",
            ],
            [
                'servicio' => $vacaciones,
                'title' => 'Cómo solicitar tus días de vacaciones',
                'content' => "Antes de crear el ticket, ten a la mano: rango de fechas deseado y si ya lo platicaste con tu jefe directo. RH confirma disponibilidad y das de alta el periodo.",
            ],
        ];

        foreach ($articulos as $articulo) {
            if (! $articulo['servicio']) {
                continue;
            }

            Article::firstOrCreate(
                ['slug' => Str::slug($articulo['title'])],
                [
                    'title' => $articulo['title'],
                    'content' => $articulo['content'],
                    'service_id' => $articulo['servicio']->id,
                    'department_id' => $articulo['servicio']->department_id,
                    'published' => true,
                ],
            );
        }
    }
}
