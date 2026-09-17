<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopOrder extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'status', 'name', 'email', 'phone', 'company_name',
        'shipping_address_line1', 'shipping_city', 'shipping_state', 'shipping_zip',
        'billing_same_as_shipping', 'billing_address_line1', 'billing_city',
        'billing_state', 'billing_zip', 'billing_company_name', 'additional_instructions',
        'subtotal', 'vat_amount', 'total',
        'payment_status', 'payment_method', 'stripe_payment_intent_id', 'paid_at',
        'tracking_number',
    ];

    protected $casts = [
        'billing_same_as_shipping' => 'boolean',
        'subtotal' => 'float',
        'vat_amount' => 'float',
        'total' => 'float',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }
}
