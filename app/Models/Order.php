<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'status', 'name', 'email', 'phone', 'company_name',
        'shipping_address_line1', 'shipping_address_line2', 'shipping_city',
        'shipping_postcode', 'shipping_country',
        'billing_same_as_shipping', 'billing_name', 'billing_company_name',
        'billing_address_line1', 'billing_address_line2', 'billing_city',
        'billing_postcode', 'billing_country',
        'notes', 'subtotal', 'vat_amount', 'delivery_fee', 'total',
        'payment_status', 'payment_method', 'stripe_payment_intent_id', 'paid_at',
    ];

    protected $casts = [
        'billing_same_as_shipping' => 'boolean',
        'subtotal' => 'float',
        'vat_amount' => 'float',
        'delivery_fee' => 'float',
        'total' => 'float',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
