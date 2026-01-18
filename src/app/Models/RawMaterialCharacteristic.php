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

    /**
     * Get a descriptive value (e.g., "0.35 mm" or "Rojo")
     */
    public function getDescriptionAttribute()
    {
        if ($this->decimal_value !== null) {
            return $this->decimal_value . ($this->unit ? ' ' . $this->unit : '');
        }
        return $this->text_value ?? 'N/A';
    }
}
