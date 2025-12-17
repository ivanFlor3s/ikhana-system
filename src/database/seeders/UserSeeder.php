<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los roles
        $adminRole = Role::where('name', 'Admin')->first();
        $empleadoRole = Role::where('name', 'Empleado')->first();
        $consultorRole = Role::where('name', 'Consultor')->first();

        // Crear usuarios dummy
        $users = [
            [
                'name' => 'Administrador',
                'email' => 'admin@ikhana.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
            ],
            [
                'name' => 'Juan Empleado',
                'email' => 'empleado@ikhana.com',
                'password' => Hash::make('empleado123'),
                'role_id' => $empleadoRole->id,
            ],
            [
                'name' => 'María Consultora',
                'email' => 'consultor@ikhana.com',
                'password' => Hash::make('consultor123'),
                'role_id' => $consultorRole->id,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
