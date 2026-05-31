<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id'             => $this->id,
            'entry_number'   => $this->entry_number,
            'remito'         => $this->remito,
            'batch'          => $this->batch,
            'entry_date'     => $this->entry_date?->format('Y-m-d'),
            'status'         => $this->status,
            'observations'   => $this->observations,
            'type'           => new RawMaterialTypeResource($this->whenLoaded('type')),
            'provider'       => new ProviderSummaryResource($this->whenLoaded('provider')),
        ];

        if ($this->relationLoaded('cobreDetail')) {
            $detail = $this->cobreDetail;
            $data['quantity_kg'] = $detail?->quantity_kg;
            $data['coils_count'] = $detail?->coils_count;
            $data['characteristic'] = $detail && $detail->relationLoaded('characteristic')
                ? new RawMaterialCharacteristicResource($detail->characteristic)
                : null;
            $data['test'] = new MaterialTestResource($this->whenLoaded('cobreTest'));
        } elseif ($this->relationLoaded('cuerdaDetail')) {
            $detail = $this->cuerdaDetail;
            $data['quantity_kg'] = $detail?->quantity_kg;
            $data['coils_count'] = $detail?->coils_count;
            $data['characteristic'] = $detail && $detail->relationLoaded('characteristic')
                ? new RawMaterialCharacteristicResource($detail->characteristic)
                : null;
            $data['test'] = new MaterialTestResource($this->whenLoaded('cuerdaTest'));
        }

        $data['returned_coils_count'] = $this->returned_coils_count;

        return $data;
    }
}
