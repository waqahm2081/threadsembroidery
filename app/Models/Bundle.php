<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    protected $fillable = [
        'slug', 'name', 'icon', 'tagline', 'badge', 'blurb', 'price', 'save_amount',
        'methods', 'featured', 'popular', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'save_amount' => 'float',
        'methods' => 'array',
        'featured' => 'boolean',
        'popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function includes(): HasMany
    {
        return $this->hasMany(BundleInclude::class)->orderBy('sort_order');
    }

    public function garments(): HasMany
    {
        return $this->hasMany(BundleGarment::class)->orderBy('sort_order');
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->slug,
            'db_id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'tagline' => $this->tagline,
            'badge' => $this->badge ?: '',
            'popular' => $this->popular,
            'featured' => $this->featured,
            'price' => (float) $this->price,
            'save' => (float) $this->save_amount,
            'blurb' => $this->blurb,
            'includes' => $this->includes->pluck('text')->values()->all(),
            'garmentCats' => $this->garments->pluck('category_slug')->unique()->values()->all(),
            'methods' => $this->methods ?? [],
            'baseGarments' => $this->garments->map(function (BundleGarment $g) {
                $colours = $g->colours
                    ->where('is_active', true)
                    ->values()
                    ->map(fn (BundleColour $c) => $c->toFrontend())
                    ->all();

                return [
                    'cat' => $g->category_slug,
                    'qty' => (int) $g->qty,
                    'label' => $g->label,
                    'guide' => $g->size_guide_key,
                    'colours' => $colours,
                ];
            })->values()->all(),
        ];
    }
}
