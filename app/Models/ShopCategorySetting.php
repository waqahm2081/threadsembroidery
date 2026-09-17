<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategorySetting extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'vat_enabled',
        'vat_percent',
    ];

    protected $casts = [
        'vat_enabled' => 'boolean',
        'vat_percent' => 'float',
    ];
}
