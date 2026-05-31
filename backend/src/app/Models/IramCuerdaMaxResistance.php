<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IramCuerdaMaxResistance extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_characteristic_id',
        'max_resistance_ohm_km',
    ];

    public function characteristic()
    {
        return $this->belongsTo(RawMaterialCharacteristic::class, 'raw_material_characteristic_id');
    }
}
