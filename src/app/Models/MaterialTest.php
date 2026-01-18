<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class MaterialTest extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'raw_material_entry_id',
        'test_date',
        'resistance_ohm_km',
        'elongation_pct',
        'check_winding',
        'check_cleanliness',
        'check_packaging',
        'check_identification',
        'result',
        'conducted_by',
        'approved_by'
    ];

    protected $casts = [
        'test_date' => 'date',
        'resistance_ohm_km' => 'float',
        'elongation_pct' => 'float',
        'check_winding' => 'boolean',
        'check_cleanliness' => 'boolean',
        'check_packaging' => 'boolean',
        'check_identification' => 'boolean',
    ];

    public function entry()
    {
        return $this->belongsTo(RawMaterialEntry::class, 'raw_material_entry_id');
    }
}
