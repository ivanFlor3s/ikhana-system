<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agreement;

class AgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agreements = [
            [
                'code' => 'convenio_multilateral',
                'name' => 'Convenio Multilateral',
                'description' => 'Convenio Multilateral del Impuesto sobre los Ingresos Brutos',
                'is_active' => true
            ],
        ];

        foreach ($agreements as $agreement) {
            Agreement::firstOrCreate(
                ['code' => $agreement['code']],
                $agreement
            );
        }
    }
}

