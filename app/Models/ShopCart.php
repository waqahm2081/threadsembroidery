<?php

namespace App\Models;

use App\Services\ShopPricingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopCart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopCartItem::class);
    }

    public function itemCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return ShopPricingService::cartSubtotalExVat($this);
    }

    public function vatRate(): float
    {
        return ShopPricingService::cartVatRate($this);
    }

    public function vatPercent(): float
    {
        return ShopPricingService::cartVatPercent($this);
    }

    public function vatAmount(): float
    {
        return ShopPricingService::vatAmount($this->subtotal(), $this->vatRate());
    }

    public function total(): float
    {
        return ShopPricingService::totalIncVat($this->subtotal(), $this->vatRate());
    }
}
