<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Administrador del sistema con acceso completo',
            ],
            [
                'name' => 'Empleado',
                'description' => 'Empleado con permisos de creación y edición',
            ],
            [
                'name' => 'Consultor',
                'description' => 'Consultor con acceso de solo lectura',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
