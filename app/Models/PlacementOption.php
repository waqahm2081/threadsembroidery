<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementOption extends Model
{
    protected $fillable = [
        'slug', 'label', 'method', 'price', 'sizes', 'included_in_bundle', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'sizes' => 'array',
        'included_in_bundle' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function toFrontend(): array
    {
        return [
            'id' => $this->slug,
            'label' => $this->label,
            'method' => $this->method,
            'price' => (float) $this->price,
            'sizes' => $this->sizes ?? [],
            'included' => $this->included_in_bundle,
        ];
    }
}
