<?php

namespace App\Models;

use App\Services\PricingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
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
        return $this->hasMany(CartItem::class);
    }

    public function itemCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return PricingService::cartSubtotalExVat($this);
    }

    public function vatAmount(): float
    {
        return PricingService::vatAmount($this->subtotal());
    }

    public function total(): float
    {
        return PricingService::totalIncVat($this->subtotal());
    }
}
