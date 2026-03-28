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
                'name' => 'Cobre',
                'description' => 'Filamento de cobre',
            ],
            [
                'name' => 'Cuerda',
                'description' => 'Filamentos de cobre ya tejidos',
            ],
            [
                'name' => 'PVC',
                'description' => 'Policlorulo de vinilo',
            ],
            [
                'name' => 'Master',
                'description' => 'Master',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}