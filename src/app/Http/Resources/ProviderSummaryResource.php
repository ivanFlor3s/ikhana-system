<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight provider representation for list views.
 * Use a full ProviderResource for detail endpoints if needed.
 */
class ProviderSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'fantasy_name'  => $this->fantasy_name,
            'business_name' => $this->business_name,
        ];
    }
}
