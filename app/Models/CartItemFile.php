<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItemFile extends Model
{
    protected $fillable = [
        'cart_item_id', 'placement_slug', 'placement_label', 'path', 'original_name',
    ];

    public function cartItem(): BelongsTo
    {
        return $this->belongsTo(CartItem::class);
    }
}
