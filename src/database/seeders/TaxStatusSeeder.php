<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxStatus;

class TaxStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxStatuses = [
            ['code' => '1', 'name' => 'IVA Responsable Inscripto', 'is_active' => true],
            ['code' => '2', 'name' => 'IVA Responsable no Inscripto', 'is_active' => true],
            ['code' => '3', 'name' => 'IVA no Responsable', 'is_active' => true],
            ['code' => '4', 'name' => 'IVA Sujeto Exento', 'is_active' => true],
            ['code' => '5', 'name' => 'Consumidor Final', 'is_active' => true],
            ['code' => '6', 'name' => 'Responsable Monotributo', 'is_active' => true],
            ['code' => '7', 'name' => 'Sujeto no Categorizado', 'is_active' => true],
            ['code' => '8', 'name' => 'Proveedor del Exterior', 'is_active' => true],
            ['code' => '9', 'name' => 'Cliente del Exterior', 'is_active' => true],
            ['code' => '10', 'name' => 'IVA Liberado – Ley Nº 19.640', 'is_active' => true],
            ['code' => '11', 'name' => 'IVA Responsable Inscripto – Agente de Percepción', 'is_active' => true],
            ['code' => '12', 'name' => 'Pequeño Contribuyente Eventual', 'is_active' => true],
            ['code' => '13', 'name' => 'Monotributista Social', 'is_active' => true],
            ['code' => '14', 'name' => 'Pequeño Contribuyente Eventual Social', 'is_active' => true],
        ];

        foreach ($taxStatuses as $taxStatus) {
            TaxStatus::firstOrCreate(
                ['code' => $taxStatus['code']],
                $taxStatus
            );
        }
    }
}

