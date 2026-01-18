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

class RawMaterialEntryLabelTest extends TestCase
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

        // Setup Data
        $this->copper = RawMaterialType::create(['name' => 'Cobre']);

        $this->characteristic = RawMaterialCharacteristic::create([
            'raw_material_type_id' => $this->copper->id,
            'name' => 'Diámetro',
            'decimal_value' => 0.35,
            'unit' => 'mm'
        ]);

        $this->provider = Provider::factory()->create();
    }

    public function test_can_download_label_pdf()
    {
        // 1. Create Entry
        $entry = RawMaterialEntry::create([
            'raw_material_type_id' => $this->copper->id,
            'provider_id' => $this->provider->id,
            'raw_material_characteristic_id' => $this->characteristic->id,
            'entry_date' => '2024-09-04',
            'remito' => '1-1925',
            'batch' => 'Lote25',
            'entry_number' => 71,
            'quantity_kg' => 1028.00,
            'coils_count' => 37,
            'status' => 'approved'
        ]);

        // Create Test (Optional but good for label content)
        $entry->test()->create([
            'test_date' => '2024-09-04',
            'resistance_ohm_km' => 184.50,
            'check_winding' => true,
            'check_cleanliness' => true,
            'check_packaging' => true,
            'check_identification' => true,
            'conducted_by' => 'Tester',
            'result' => 'OK'
        ]);

        // 2. Call Endpoint
        $response = $this->get("/api/raw-material-entries/{$entry->id}/label");

        // 3. Assert Response
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        // Assert filename in header (Relaxed quote check or regex)
        // Laravel framework returns: attachment; filename=Etiqueta_Entrada_71.pdf
        $response->assertHeader('Content-Disposition', 'attachment; filename=Etiqueta_Entrada_71.pdf');
    }
}
