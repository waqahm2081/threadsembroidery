<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrderItem extends Model
{
    protected $fillable = [
        'shop_order_id', 'product_id', 'style_code', 'product_title', 'category_slug',
        'colour_name', 'colour_code', 'size', 'size_breakdown', 'quantity',
        'unit_price', 'decoration_fee', 'logo_setup_fee', 'line_total',
        'design_data', 'design_file_path', 'design_assets',
    ];

    protected $casts = [
        'size_breakdown' => 'array',
        'design_data' => 'array',
        'design_assets' => 'array',
        'unit_price' => 'float',
        'decoration_fee' => 'float',
        'logo_setup_fee' => 'float',
        'line_total' => 'float',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'shop_order_id');
    }
}
