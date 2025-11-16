<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fantasy_name',
        'business_name',
        'cuit',
        'iibb',
        'tax_status',
        'agreement',
        'phone_1',
        'phone_2',
        'phone_3',
        'phone_4',
        'phone_5',
        'email_1',
        'email_2',
        'email_3',
        'email_4',
        'email_5',
        'address',
        'website',
        'contact_name',
        'observations',
        'business_hours_start',
        'business_hours_end',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_hours_start' => 'datetime:H:i',
        'business_hours_end' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

