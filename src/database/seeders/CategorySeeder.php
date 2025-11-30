<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Construcción',
                'description' => 'Materiales y servicios de construcción, arquitectura y obra',
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Equipos informáticos, software y servicios tecnológicos',
            ],
            [
                'name' => 'Alimentación',
                'description' => 'Productos alimenticios, bebidas y servicios de catering',
            ],
            [
                'name' => 'Textil e Indumentaria',
                'description' => 'Ropa, calzado, telas y accesorios',
            ],
            [
                'name' => 'Transporte y Logística',
                'description' => 'Servicios de transporte, envíos y logística',
            ],
            [
                'name' => 'Servicios Profesionales',
                'description' => 'Servicios de consultoría, contabilidad, legales y otros profesionales',
            ],
            [
                'name' => 'Limpieza y Mantenimiento',
                'description' => 'Productos y servicios de limpieza y mantenimiento',
            ],
            [
                'name' => 'Papelería y Librería',
                'description' => 'Artículos de oficina, papelería y librería',
            ],
            [
                'name' => 'Muebles y Decoración',
                'description' => 'Muebles, artículos de decoración y equipamiento',
            ],
            [
                'name' => 'Salud y Medicina',
                'description' => 'Productos médicos, farmacéuticos y servicios de salud',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

