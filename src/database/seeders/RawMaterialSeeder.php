<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterialType;
use App\Models\RawMaterialCharacteristic;
use App\Models\IramCopperMaxResistance;

class RawMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tipos de Materia Prima
        $types = [
            'Cobre',
            'PVC',
            'Master',
            'Varios'
        ];

        foreach ($types as $typeName) {
            RawMaterialType::firstOrCreate(['name' => $typeName]);
        }

        // Obtener el tipo "Cobre"
        $cobre = RawMaterialType::where('name', 'Cobre')->first();

        // 2. Características de Cobre: diámetros y sus resistencias máximas (IRAM)
        $diametros = [
            // [Diametro (mm), Max Resistance (Ohm/km)]
            [0.30, 256.00],
            [0.35, 184.60],
            [0.38, 156.50],
            [0.40, 141.30],
            [0.50, 90.44],
            [0.60, 62.81],
            [0.67, 50.50],
            [0.85, 31.29],
            [1.05, 20.51],
            [1.13, 17.70],
            [1.35, 12.41],
        ];

        foreach ($diametros as $data) {
            $diameter = $data[0];
            $maxResistance = $data[1];

            // Crear característica "Diámetro X mm" para Cobre
            $characteristic = RawMaterialCharacteristic::firstOrCreate(
                [
                    'raw_material_type_id' => $cobre->id,
                    'name' => 'Diámetro',
                    'decimal_value' => $diameter
                ],
                [
                    'unit' => 'mm',
                    'text_value' => null
                ]
            );

            // 3. Crear regla IRAM asociada
            IramCopperMaxResistance::updateOrCreate(
                ['raw_material_characteristic_id' => $characteristic->id],
                ['max_resistance_ohm_km' => $maxResistance]
            );
        }
    }
}
