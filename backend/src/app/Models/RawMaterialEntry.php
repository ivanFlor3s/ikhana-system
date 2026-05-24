<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class RawMaterialEntry extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'raw_material_type_id',
        'provider_id',
        'raw_material_characteristic_id',
        'entry_number',
        'remito',
        'batch',
        'entry_date',
        'quantity_kg',
        'coils_count',
        'status',
        'observations'
    ];

    protected $appends = [
        'returned_coils_count',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'quantity_kg' => 'float',
        'batch' => 'integer',
        'returned_coils_count' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(RawMaterialType::class, 'raw_material_type_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function characteristic()
    {
        return $this->belongsTo(RawMaterialCharacteristic::class, 'raw_material_characteristic_id');
    }

    public function test()
    {
        return $this->hasOne(MaterialTest::class);
    }

    public function coilMovements()
    {
        return $this->hasMany(ProviderCoilMovement::class, 'raw_material_entry_id');
    }

    public function getReturnedCoilsCountAttribute(): int
    {
        return (int) $this->coilMovements()->sum('coils_returned');
    }
}
