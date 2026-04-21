<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Formats a provider row for the coils-per-provider summary list.
 */
class ProviderCoilSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'provider_id'       => $this->id,
            'provider_name'     => $this->fantasy_name ?? $this->business_name,
            'coils_count'       => (int) ($this->coils_count ?? 0),
            'last_movement_in'  => $this->last_movement_in,
            'last_movement_out' => $this->last_movement_out,
        ];
    }
}
