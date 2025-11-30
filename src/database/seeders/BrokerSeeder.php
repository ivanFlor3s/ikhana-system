<?php

namespace Database\Seeders;

use App\Models\Broker;
use Illuminate\Database\Seeder;

class BrokerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brokers = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan.perez@corredores.com',
                'phone' => '+54 11 4444-5555',
            ],
            [
                'first_name' => 'María',
                'last_name' => 'González',
                'email' => 'maria.gonzalez@brokers.com',
                'phone' => '+54 11 5555-6666',
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Rodríguez',
                'email' => 'carlos.rodriguez@contactos.com',
                'phone' => '+54 11 6666-7777',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Martínez',
                'email' => 'ana.martinez@agentes.com',
                'phone' => '+54 11 7777-8888',
            ],
            [
                'first_name' => 'Roberto',
                'last_name' => 'López',
                'email' => 'roberto.lopez@intermediarios.com',
                'phone' => '+54 11 8888-9999',
            ],
        ];

        foreach ($brokers as $broker) {
            Broker::create($broker);
        }
    }
}

