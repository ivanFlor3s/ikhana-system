<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialCharacteristic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raw_material_type_id',
        'name',
        'decimal_value',
        'text_value',
        'unit'
    ];

    public function type()
    {
        return $this->belongsTo(RawMaterialType::class, 'raw_material_type_id');
    }

    public function iramCopperMaxResistance()
    {
        return $this->hasOne(IramCopperMaxResistance::class);
    }

    public function iramCuerdaMaxResistance()
    {
        return $this->hasOne(IramCuerdaMaxResistance::class);
    }

    public function getIramMaxResistanceAttribute()
    {
        return $this->iramCopperMaxResistance ?? $this->iramCuerdaMaxResistance;
    }

    public function getDecimalValueAttribute($value)
    {
        return $value !== null ? number_format((float) $value, 2, '.', '') : null;
    }

    public function getDescriptionAttribute()
    {
        if ($this->decimal_value !== null) {
            return $this->decimal_value . ($this->unit ? ' ' . $this->unit : '');
        }
        return $this->text_value ?? 'N/A';
    }
}
