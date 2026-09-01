<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCartItem extends Model
{
    protected $fillable = [
        'shop_cart_id',
        'product_id',
        'style_code',
        'product_title',
        'category_slug',
        'product_slug',
        'colour_code',
        'colour_name',
        'colour_hex',
        'size',
        'size_breakdown',
        'quantity',
        'unit_price',
        'decoration_fee',
        'logo_setup_fee',
        'is_customized',
        'design_data',
        'design_file_path',
        'design_assets',
    ];

    protected $casts = [
        'size_breakdown' => 'array',
        'design_data' => 'array',
        'design_assets' => 'array',
        'is_customized' => 'boolean',
        'unit_price' => 'float',
        'decoration_fee' => 'float',
        'logo_setup_fee' => 'float',
        'quantity' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShopCart::class, 'shop_cart_id');
    }

    public function lineTotal(): float
    {
        return (($this->unit_price + $this->decoration_fee) * $this->quantity) + $this->logo_setup_fee;
    }

    public function customizerUrl(): string
    {
        return route('shop.product', [
            'categorySlug' => $this->category_slug,
            'productSlug' => $this->product_slug,
        ]).'?'.http_build_query(array_filter([
            'cart_item_id' => $this->id,
            'size' => $this->size,
            'qty' => $this->quantity,
            'colour' => $this->colour_code,
        ]));
    }
}
