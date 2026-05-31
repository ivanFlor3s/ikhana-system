<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CobreEntryDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raw_material_entry_id',
        'raw_material_characteristic_id',
        'quantity_kg',
        'coils_count',
    ];

    protected $casts = [
        'quantity_kg' => 'float',
    ];

    public function entry()
    {
        return $this->belongsTo(RawMaterialEntry::class, 'raw_material_entry_id');
    }

    public function characteristic()
    {
        return $this->belongsTo(RawMaterialCharacteristic::class, 'raw_material_characteristic_id');
    }
}
