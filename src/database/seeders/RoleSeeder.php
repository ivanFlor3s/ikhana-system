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
                'name' => 'Operador',
                'description' => 'Usuario con permisos de creación y edición justos para cumplir sus tareas',
            ],
            [
                'name' => 'Administracion',
                'description' => 'Usuario con permisos de creación y edición, mas involucrado en operactiones de administracion',
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