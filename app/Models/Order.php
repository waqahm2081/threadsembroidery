<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'name',
        'email',
        'phone',
        'company_name',
        'shipping_address_line1',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'billing_same_as_shipping',
        'billing_address_line1',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_company_name',
        'additional_instructions',
        'subtotal',
        'vat_amount',
        'total',
        'estimated_delivery_at',
    ];

    protected $casts = [
        'billing_same_as_shipping' => 'boolean',
        'subtotal' => 'float',
        'vat_amount' => 'float',
        'total' => 'float',
        'estimated_delivery_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(OrderTrackingEvent::class)->orderBy('sort_order');
    }

    public function displayStatus(): string
    {
        return match ($this->status) {
            'pending', 'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function filterStatus(): string
    {
        return match ($this->status) {
            'pending', 'processing' => 'processing',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            default => $this->status,
        };
    }
}
