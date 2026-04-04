<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'entry_number'   => $this->entry_number,
            'remito'         => $this->remito,
            'batch'          => $this->batch,
            'entry_date'     => $this->entry_date?->format('Y-m-d'),
            'quantity_kg'    => $this->quantity_kg,
            'coils_count'    => $this->coils_count,
            'status'         => $this->status,
            'observations'   => $this->observations,

            // Nested resources — only included when the relationship was eager-loaded
            'type'           => new RawMaterialTypeResource($this->whenLoaded('type')),
            'provider'       => new ProviderSummaryResource($this->whenLoaded('provider')),
            'characteristic' => new RawMaterialCharacteristicResource($this->whenLoaded('characteristic')),
            'test'           => new MaterialTestResource($this->whenLoaded('test')),
        ];
    }
}
