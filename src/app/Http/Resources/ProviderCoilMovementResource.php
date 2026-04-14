<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Formats a single coil movement row for the provider movements list.
 */
class ProviderCoilMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'date'           => $this->created_at?->toIso8601String(),
            'coils_received' => $this->coils_received,
            'coils_returned' => $this->coils_returned,
            'entry'          => $this->when($this->relationLoaded('entry') && $this->entry, [
                'id'           => $this->entry?->id,
                'entry_number' => $this->entry?->entry_number,
                'remito'       => $this->entry?->remito,
            ]),
        ];
    }
}
