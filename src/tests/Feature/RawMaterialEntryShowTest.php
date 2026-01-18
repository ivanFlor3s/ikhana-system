<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\RawMaterialType;
use App\Models\RawMaterialCharacteristic;
use App\Models\IramCopperMaxResistance;
use App\Models\Provider;
use App\Models\RawMaterialEntry;
use Laravel\Sanctum\Sanctum;

class RawMaterialEntryShowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $copper;
    protected $characteristic;
    protected $provider;
    protected $iramRule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Setup Data
        $this->copper = RawMaterialType::create(['name' => 'Cobre']);

        $this->characteristic = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $this->copper->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.38,
            'unit' => 'mm'
        ]);

        $this->iramRule = IramCopperMaxResistance::create([
            'raw_material_characteristic_id' => $this->characteristic->id,
            'max_resistance_ohm_km' => 155.20
        ]);

        $this->provider = Provider::factory()->create(['business_name' => 'Rio Batel']);
    }

    public function test_can_retrieve_entry_details_for_report()
    {
        // 1. Create Entry
        $entry = RawMaterialEntry::create([
            'raw_material_type_id' => $this->copper->id,
            'provider_id' => $this->provider->id,
            'raw_material_characteristic_id' => $this->characteristic->id,
            'entry_date' => '2025-03-28',
            'remito' => '1-1764',
            'batch' => '1764',
            'quantity_kg' => 1010.700,
            'coils_count' => 5,
            'status' => 'approved'
        ]);

        // 2. Create Associated Test
        $entry->test()->create([
            'test_date' => '2025-03-28',
            'resistance_ohm_km' => 155.20,
            'check_winding' => true,
            'check_cleanliness' => true,
            'check_packaging' => true,
            'check_identification' => true,
            'conducted_by' => 'Juan Perez',
            'result' => 'OK'
        ]);

        // 3. Call Endpoint
        $response = $this->getJson("/api/raw-material-entries/{$entry->id}");

        // 4. Assert Structure and Data
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            // Header Info
            ->assertJsonPath('data.remito', '1-1764')
            ->assertJsonPath('data.provider.business_name', 'Rio Batel')
            ->assertJsonPath('data.type.name', 'Cobre')
            ->assertJsonPath('data.quantity_kg', 1010.700)

            // Characteristic & IRAM (Valores a cumplir)
            // Note: The controller appends 'description', let's check exact decimal value too
            ->assertJsonPath('data.characteristic.decimal_value', 0.38)
            ->assertJsonPath('data.characteristic.iram_copper_max_resistance.max_resistance_ohm_km', 155.20)

            // Test Results (Valores Medidos)
            ->assertJsonPath('data.test.resistance_ohm_km', 155.20)
            ->assertJsonPath('data.test.check_winding', true)
            ->assertJsonPath('data.test.conducted_by', 'Juan Perez')
            ->assertJsonPath('data.test.result', 'OK');
    }

    public function test_show_returns_404_for_non_existent_entry()
    {
        $response = $this->getJson("/api/raw-material-entries/99999");
        $response->assertStatus(404);
    }
}
