<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Departments\Models\Department;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Una categoría "General" por departamento como punto de partida;
     * se pueden desglosar más categorías después sin tocar código.
     */
    public function run(): void
    {
        Department::all()->each(function (Department $departamento) {
            Category::firstOrCreate([
                'department_id' => $departamento->id,
                'name' => 'General',
            ]);
        });
    }
}
