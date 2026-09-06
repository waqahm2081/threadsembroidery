<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BundleColour extends Model
{
    protected $fillable = [
        'name', 'slug', 'hex', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function garments(): BelongsToMany
    {
        return $this->belongsToMany(BundleGarment::class, 'bundle_garment_colour')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'hex' => $this->hex,
        ];
    }
}
