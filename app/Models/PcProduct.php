<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PcProduct extends Model
{
    protected $table = 'pc_products';

    protected $casts = [
        'price_single' => 'float',
        'price_pack' => 'float',
        'price_carton' => 'float',
        'is_clearance' => 'boolean',
        'is_discontinued' => 'boolean',
        'is_new' => 'boolean',
        'is_special_order' => 'boolean',
    ];

    public function customizerImage(): HasOne
    {
        return $this->hasOne(PcProductCustomizerImage::class, 'product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_discontinued', 0);
    }
}
