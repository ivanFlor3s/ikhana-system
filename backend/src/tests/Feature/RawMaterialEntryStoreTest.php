<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\RawMaterialType;
use App\Models\RawMaterialCharacteristic;
use App\Models\IramCopperMaxResistance;
use App\Models\Provider;
use Laravel\Sanctum\Sanctum;

class RawMaterialEntryStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $copper;
    protected $characteristic;
    protected $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Setup common data
        $this->copper = RawMaterialType::create(['name' => 'Cobre']);
        $this->characteristic = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $this->copper->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.35,
            'unit' => 'mm'
        ]);
        IramCopperMaxResistance::create([
            'raw_material_characteristic_id' => $this->characteristic->id,
            'max_resistance_ohm_km' => 184.60
        ]);
        $this->provider = Provider::factory()->create();
    }

    public function test_can_create_entry_with_approved_test()
    {
        $payload = [
            'raw_material_type_id' => $this->copper->id,
            'provider_id' => $this->provider->id,
            'raw_material_characteristic_id' => $this->characteristic->id,
            'entry_date' => '2024-09-04',
            'remito' => 'R-SUCCESS',
            'batch' => 'BATCH-OK',
            'quantity_kg' => 1000.5,
            'coils_count' => 10,

            // Mandatory Test Data
            'test' => [
                'resistance_ohm_km' => 180.00, // Valid (< 184.60)
                'check_winding' => true,
                'check_cleanliness' => true,
                'check_packaging' => true,
                'check_identification' => true,
                'conducted_by' => 'Tester User'
            ]
        ];

        $response = $this->postJson('/api/raw-material-entries', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'approved') // Should be approved
            ->assertJsonPath('data.test.result', 'OK'); // Should be OK

        $this->assertDatabaseHas('raw_material_entries', ['remito' => 'R-SUCCESS']);
        $this->assertDatabaseHas('material_tests', ['result' => 'OK']);
    }

    public function test_can_create_entry_with_rejected_test_due_to_resistance()
    {
        $payload = [
            'raw_material_type_id' => $this->copper->id,
            'provider_id' => $this->provider->id,
            'raw_material_characteristic_id' => $this->characteristic->id,
            'entry_date' => '2024-09-04',
            'remito' => 'R-FAIL-RES',
            'batch' => 'BATCH-BAD',
            'quantity_kg' => 1000,
            'coils_count' => 10,

            'test' => [
                'resistance_ohm_km' => 190.00, // Invalid (> 184.60)
                'check_winding' => true,
                'check_cleanliness' => true,
                'check_packaging' => true,
                'check_identification' => true,
                'conducted_by' => 'Tester User'
            ]
        ];

        $response = $this->postJson('/api/raw-material-entries', $payload);

        // It should still be created (201), but with status rejected
        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.test.result', 'NO_OK');

        $this->assertDatabaseHas('raw_material_entries', ['remito' => 'R-FAIL-RES']);
    }

    public function test_transaction_rolls_back_if_test_data_missing()
    {
        $payload = [
            'raw_material_type_id' => $this->copper->id,
            'provider_id' => $this->provider->id,
            'raw_material_characteristic_id' => $this->characteristic->id,
            'entry_date' => '2024-09-04',
            'remito' => 'R-ROLLBACK',
            'batch' => 'BATCH-INC',
            'quantity_kg' => 1000,
            'coils_count' => 10,

            // Missing 'test' array entirely
        ];

        $response = $this->postJson('/api/raw-material-entries', $payload);

        $response->assertStatus(422); // Validation error

        // Assert entry was NOT created
        $this->assertDatabaseMissing('raw_material_entries', ['remito' => 'R-ROLLBACK']);
    }
}
