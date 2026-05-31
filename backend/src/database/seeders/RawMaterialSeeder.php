<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterialType;
use App\Models\RawMaterialCharacteristic;
use App\Models\IramCopperMaxResistance;
use App\Models\IramCuerdaMaxResistance;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Cobre',
            'PVC',
            'Master',
            'Varios',
            'Cuerda'
        ];

        foreach ($types as $typeName) {
            RawMaterialType::firstOrCreate(['name' => $typeName]);
        }

        $cobre = RawMaterialType::where('name', 'Cobre')->first();
        $cuerda = RawMaterialType::where('name', 'Cuerda')->first();

        $cobreDiameters = [
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

        foreach ($cobreDiameters as $data) {
            $diameter = $data[0];
            $maxResistance = $data[1];

            $characteristic = RawMaterialCharacteristic::firstOrCreate(
                [
                    'raw_material_type_id' => $cobre->id,
                    'name' => 'Diametro',
                    'decimal_value' => $diameter
                ],
                [
                    'unit' => 'mm',
                    'text_value' => null
                ]
            );

            IramCopperMaxResistance::updateOrCreate(
                ['raw_material_characteristic_id' => $characteristic->id],
                ['max_resistance_ohm_km' => $maxResistance]
            );
        }

        $cuerdaSecciones = [
            [0.75, 26],
            [1.00, 19.5],
            [1.50, 13.3],
            [2.50, 7.98],
            [4.00, 4.95],
            [6.00, 3.3],
            [10.00, 1.91],
            [16.00, 1.21],
            [25.00, 0.78],
            [35.00, 0.554],
            [50.00, 0.386],
            [70.00, 0.272],
            [95.00, 0.206],
            [120.00, 0.161],
        ];

        foreach ($cuerdaSecciones as $data) {
            $seccion = $data[0];
            $maxResistance = $data[1];

            $characteristic = RawMaterialCharacteristic::firstOrCreate(
                [
                    'raw_material_type_id' => $cuerda->id,
                    'name' => 'Seccion',
                    'decimal_value' => $seccion
                ],
                [
                    'unit' => 'mm²',
                    'text_value' => null
                ]
            );

            IramCuerdaMaxResistance::updateOrCreate(
                ['raw_material_characteristic_id' => $characteristic->id],
                ['max_resistance_ohm_km' => $maxResistance]
            );
        }
    }
}
