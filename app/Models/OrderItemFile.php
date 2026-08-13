<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemFile extends Model
{
    protected $fillable = [
        'order_item_id', 'placement_slug', 'placement_label', 'path', 'original_name',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
