<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'item_type', 'bundle_id', 'title', 'image_path',
        'colour_name', 'colour_hex', 'method', 'size_breakdown', 'garments_snapshot',
        'addons', 'quantity', 'base_price', 'addon_total', 'line_total', 'summary',
    ];

    protected $casts = [
        'size_breakdown' => 'array',
        'garments_snapshot' => 'array',
        'addons' => 'array',
        'quantity' => 'integer',
        'base_price' => 'float',
        'addon_total' => 'float',
        'line_total' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderItemFile::class);
    }
}
