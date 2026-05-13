<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function characteristics()
    {
        return $this->hasMany(RawMaterialCharacteristic::class);
    }
}
