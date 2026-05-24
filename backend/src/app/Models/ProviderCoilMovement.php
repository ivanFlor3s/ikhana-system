<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderCoilMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'raw_material_entry_id',
        'type',
        'coils_received',
        'coils_returned',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function entry()
    {
        return $this->belongsTo(RawMaterialEntry::class, 'raw_material_entry_id');
    }
}
