<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'test_date'            => $this->test_date?->format('Y-m-d'),
            'resistance_ohm_km'    => $this->resistance_ohm_km,
            'elongation_pct'       => $this->elongation_pct,
            'check_winding'        => $this->check_winding,
            'check_cleanliness'    => $this->check_cleanliness,
            'check_packaging'      => $this->check_packaging,
            'check_identification' => $this->check_identification,
            'result'               => $this->result,
            'conducted_by'         => $this->conducted_by,
        ];
    }
}
