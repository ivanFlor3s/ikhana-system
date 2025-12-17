<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders de datos maestros
        $this->call([
                // Primero roles y usuarios
            RoleSeeder::class,
            UserSeeder::class,

                // Luego datos del sistema
            TaxStatusSeeder::class,
            AgreementSeeder::class,
            CategorySeeder::class,
            BrokerSeeder::class,
        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

