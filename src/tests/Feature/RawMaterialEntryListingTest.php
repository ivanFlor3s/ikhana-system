<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\RawMaterialType;
use App\Models\RawMaterialCharacteristic;
use App\Models\Provider;
use App\Models\RawMaterialEntry;
use Laravel\Sanctum\Sanctum;

class RawMaterialEntryListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_entries_with_combined_filters()
    {
        // 1. Setup Data
        // Types
        $copper = RawMaterialType::create(['name' => 'Cobre']);
        $pvc = RawMaterialType::create(['name' => 'PVC']);

        // Characteristics
        $diameter035 = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $copper->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.35,
            'unit' => 'mm'
        ]);
        $colorRed = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $pvc->id,
            'name' => 'Color',
            'text_value' => 'Rojo'
        ]);

        // Provider
        $providerA = Provider::factory()->create(['business_name' => 'Provider Target']);
        $providerB = Provider::factory()->create(['business_name' => 'Provider Other']);

        // 2. Create Entries

        // Match Target: Cobre, Provider Target, Date: 2024-05-15, Remito contains '123'
        $targetEntry = RawMaterialEntry::create([
            'raw_material_type_id' => $copper->id,
            'provider_id' => $providerA->id,
            'raw_material_characteristic_id' => $diameter035->id,
            'entry_number' => 1,
            'remito' => 'REQ-123-ABC',
            'batch' => 'BATCH-001',
            'entry_date' => '2024-05-15',
            'quantity_kg' => 100,
            'coils_count' => 10
        ]);

        // Miss - Wrong Type (PVC)
        RawMaterialEntry::create([
            'raw_material_type_id' => $pvc->id,
            'provider_id' => $providerA->id,
            'raw_material_characteristic_id' => $colorRed->id,
            'entry_number' => 2,
            'remito' => 'REQ-123-XYZ',
            'entry_date' => '2024-05-15',
            'quantity_kg' => 50,
            'coils_count' => 5
        ]);

        // Miss - Wrong Date (Outside Range)
        RawMaterialEntry::create([
            'raw_material_type_id' => $copper->id,
            'provider_id' => $providerA->id,
            'raw_material_characteristic_id' => $diameter035->id,
            'entry_number' => 3,
            'remito' => 'REQ-123-LATE',
            'entry_date' => '2024-06-01', // Target is May
            'quantity_kg' => 100,
            'coils_count' => 10
        ]);

        // Miss - Wrong Text (Search mismatch)
        RawMaterialEntry::create([
            'raw_material_type_id' => $copper->id,
            'provider_id' => $providerB->id, // Provider Other
            'raw_material_characteristic_id' => $diameter035->id,
            'entry_number' => 4,
            'remito' => 'REQ-999-ABC', // Doesn't match '123'
            'entry_date' => '2024-05-15',
            'quantity_kg' => 100,
            'coils_count' => 10
        ]);

        // 3. Execute Request with ALL filters
        $response = $this->getJson('/api/raw-material-entries?' . http_build_query([
            'search' => '123', // Should match remito
            'raw_material_type_id' => $copper->id,
            'date_from' => '2024-05-01',
            'date_to' => '2024-05-31',
        ]));

        // 4. Assertions
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data') // Should return exactly 1 result
            ->assertJsonPath('data.data.0.id', $targetEntry->id);
    }

    public function test_can_filter_by_characteristic()
    {
        // 1. Setup Data
        $copper = RawMaterialType::create(['name' => 'Cobre']);

        // Characteristic A: 0.35mm
        $diameter035 = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $copper->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.35,
            'unit' => 'mm'
        ]);

        // Characteristic B: 0.50mm
        $diameter050 = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $copper->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.50,
            'unit' => 'mm'
        ]);

        $provider = Provider::factory()->create();

        // Entry with 0.35mm (Target)
        $targetEntry = RawMaterialEntry::create([
            'raw_material_type_id' => $copper->id,
            'provider_id' => $provider->id,
            'raw_material_characteristic_id' => $diameter035->id,
            'entry_number' => 10,
            'remito' => 'R-035',
            'entry_date' => '2024-05-15',
            'quantity_kg' => 100,
            'coils_count' => 5
        ]);

        // Entry with 0.50mm (Should be filtered out)
        RawMaterialEntry::create([
            'raw_material_type_id' => $copper->id,
            'provider_id' => $provider->id,
            'raw_material_characteristic_id' => $diameter050->id,
            'entry_number' => 11,
            'remito' => 'R-050',
            'entry_date' => '2024-05-15',
            'quantity_kg' => 100,
            'coils_count' => 5
        ]);

        // 2. Execute Request filtering by 0.35mm
        $response = $this->getJson('/api/raw-material-entries?' . http_build_query([
            'raw_material_characteristic_id' => $diameter035->id,
        ]));

        // 3. Assertions
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $targetEntry->id)
            ->assertJsonPath('data.data.0.characteristic.decimal_value', 0.35); // Check value matches
    }
}
