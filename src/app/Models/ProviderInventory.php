<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'coils_count',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
