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
        'entry_number',
        'remito',
        'batch',
        'entry_date',
        'status',
        'observations',
    ];

    protected $appends = [
        'returned_coils_count',
    ];

    protected $casts = [
        'entry_date' => 'date',
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

    public function coilMovements()
    {
        return $this->hasMany(ProviderCoilMovement::class, 'raw_material_entry_id');
    }

    public function cobreDetail()
    {
        return $this->hasOne(CobreEntryDetail::class, 'raw_material_entry_id');
    }

    public function cuerdaDetail()
    {
        return $this->hasOne(CuerdaEntryDetail::class, 'raw_material_entry_id');
    }

    public function cobreTest()
    {
        return $this->hasOne(CobreTest::class, 'raw_material_entry_id');
    }

    public function cuerdaTest()
    {
        return $this->hasOne(CuerdaTest::class, 'raw_material_entry_id');
    }

    public function getQuantityKgAttribute()
    {
        if ($this->relationLoaded('cobreDetail') && $this->cobreDetail) {
            return $this->cobreDetail->quantity_kg;
        }
        if ($this->relationLoaded('cuerdaDetail') && $this->cuerdaDetail) {
            return $this->cuerdaDetail->quantity_kg;
        }
        return null;
    }

    public function getCoilsCountAttribute()
    {
        if ($this->relationLoaded('cobreDetail') && $this->cobreDetail) {
            return $this->cobreDetail->coils_count;
        }
        if ($this->relationLoaded('cuerdaDetail') && $this->cuerdaDetail) {
            return $this->cuerdaDetail->coils_count;
        }
        return null;
    }

    public function getCharacteristicAttribute()
    {
        if ($this->relationLoaded('cobreDetail') && $this->cobreDetail) {
            return $this->cobreDetail->characteristic;
        }
        if ($this->relationLoaded('cuerdaDetail') && $this->cuerdaDetail) {
            return $this->cuerdaDetail->characteristic;
        }
        return null;
    }

    public function getTestAttribute()
    {
        if ($this->relationLoaded('cobreTest') && $this->cobreTest) {
            return $this->cobreTest;
        }
        if ($this->relationLoaded('cuerdaTest') && $this->cuerdaTest) {
            return $this->cuerdaTest;
        }
        return null;
    }

    public function getReturnedCoilsCountAttribute(): int
    {
        return (int) $this->coilMovements()->sum('coils_returned');
    }
}
