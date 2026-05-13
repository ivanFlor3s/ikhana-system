<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderInventory;
use Illuminate\Http\JsonResponse;

class ProviderInventoryController extends Controller
{
    /**
     * Get Provider Inventory
     *
     * Retrieves the current inventory summary (like coils count) for a specific provider.
     *
     * @param int $providerId
     * @return JsonResponse
     */
    public function show($providerId): JsonResponse
    {
        $inventory = ProviderInventory::firstOrCreate(
            ['provider_id' => $providerId],
            ['coils_count' => 0]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'provider_id' => $inventory->provider_id,
                'coils_count' => $inventory->coils_count
            ],
            'message' => 'Inventario del proveedor obtenido exitosamente'
        ]);
    }
}
