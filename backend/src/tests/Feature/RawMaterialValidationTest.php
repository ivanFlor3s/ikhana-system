<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\RawMaterialType;
use App\Models\RawMaterialCharacteristic;
use App\Models\IramCopperMaxResistance;
use Laravel\Sanctum\Sanctum;

class RawMaterialValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed básico manual para el test para no depender del seeder completo si no queremos
        // Pero dado que tenemos seeders complejos, podríamos usar lógica manual ligera aquí.

        $this->user = User::factory()->create();
    }

    public function test_validate_resistance_success_within_limit()
    {
        // 1. Crear Tipo Cobre
        $type = RawMaterialType::create(['name' => 'Cobre']);

        // 2. Crear Característica 0.35mm
        $characteristic = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $type->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.35,
            'unit' => 'mm'
        ]);

        // 3. Crear Regla IRAM (Max 184.60)
        IramCopperMaxResistance::create([
            'raw_material_characteristic_id' => $characteristic->id,
            'max_resistance_ohm_km' => 184.60
        ]);

        Sanctum::actingAs($this->user);

        // 4. Probar valor válido (180.00)
        $response = $this->postJson('/api/raw-materials/validate-resistance', [
            'raw_material_characteristic_id' => $characteristic->id,
            'resistance_ohm_km' => 180.00
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'valid' => true,
                    'max_allowed' => 184.60,
                    'has_rule' => true
                ],
                'message' => 'Valor dentro de la norma'
            ]);
    }

    public function test_validate_resistance_fails_exceeding_limit()
    {
        // 1. Setup similar
        $type = RawMaterialType::create(['name' => 'Cobre']);
        $characteristic = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $type->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.35,
            'unit' => 'mm'
        ]);
        IramCopperMaxResistance::create([
            'raw_material_characteristic_id' => $characteristic->id,
            'max_resistance_ohm_km' => 184.60
        ]);

        Sanctum::actingAs($this->user);

        // 4. Probar valor inválido (190.00)
        $response = $this->postJson('/api/raw-materials/validate-resistance', [
            'raw_material_characteristic_id' => $characteristic->id,
            'resistance_ohm_km' => 190.00
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'valid' => false,
                    'max_allowed' => 184.60,
                    'has_rule' => true
                ],
                'message' => 'Valor excede el máximo permitido por norma IRAM'
            ]);
    }

    public function test_validate_resistance_success_no_rule()
    {
        // 1. Tipo PVC (sin reglas IRAM)
        $type = RawMaterialType::create(['name' => 'PVC']);
        $characteristic = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $type->id,
            'name' => 'Color',
            'text_value' => 'Rojo'
        ]);

        Sanctum::actingAs($this->user);

        // 4. Probar cualquier valor
        $response = $this->postJson('/api/raw-materials/validate-resistance', [
            'raw_material_characteristic_id' => $characteristic->id,
            'resistance_ohm_km' => 100.00
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'valid' => true,
                    'max_allowed' => null,
                    'has_rule' => false
                ],
                'message' => 'No hay regla IRAM asociada a esta característica'
            ]);
    }

    public function test_validation_input_errors()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/raw-materials/validate-resistance', [
            // Faltan campos
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['raw_material_characteristic_id', 'resistance_ohm_km']);
    }
}
