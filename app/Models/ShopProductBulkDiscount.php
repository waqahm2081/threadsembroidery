<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductBulkDiscount extends Model
{
    protected $fillable = [
        'style_code',
        'qty_from',
        'qty_to',
        'discount_percent',
        'sort_order',
    ];

    protected $casts = [
        'qty_from' => 'integer',
        'qty_to' => 'integer',
        'discount_percent' => 'float',
        'sort_order' => 'integer',
    ];

    public function containsQty(int $qty): bool
    {
        return $qty >= $this->qty_from && $qty <= $this->qty_to;
    }

    public function rangeLabel(): string
    {
        if ($this->qty_from === $this->qty_to) {
            return $this->qty_from.' items';
        }

        return $this->qty_from.'–'.$this->qty_to.' items';
    }
}
