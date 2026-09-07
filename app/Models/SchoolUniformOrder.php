<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SchoolUniformOrder extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'school_id', 'status',
        'customer_name', 'customer_email', 'customer_phone', 'company_name',
        'pupil_name', 'pupil_class',
        'shipping_address_line1', 'shipping_address_line2',
        'shipping_city', 'shipping_postcode', 'shipping_country',
        'billing_same_as_shipping', 'billing_name', 'billing_company_name',
        'billing_address_line1', 'billing_address_line2',
        'billing_city', 'billing_postcode', 'billing_country',
        'subtotal', 'vat_amount', 'delivery_fee', 'total', 'notes',
        'payment_status', 'payment_method', 'stripe_payment_intent_id', 'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'float', 'vat_amount' => 'float',
        'delivery_fee' => 'float', 'total' => 'float',
        'billing_same_as_shipping' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function items(): HasMany { return $this->hasMany(SchoolUniformOrderItem::class); }

    public static function generateNumber(): string
    {
        do {
            $number = 'SU-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (static::where('order_number', $number)->exists());
        return $number;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
}
