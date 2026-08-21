<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolProduct extends Model
{
    protected $fillable = [
        'school_id', 'school_product_category_id', 'name', 'slug',
        'description', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SchoolProductCategory::class, 'school_product_category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(SchoolProductVariant::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(SchoolProductImage::class)->orderBy('sort_order');
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $primary ? asset('storage/'.$primary->path) : null;
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'category_id' => $this->school_product_category_id,
            'category_name' => $this->category?->name,
            'primary_image' => $this->primary_image_url,
            'images' => $this->images->map(fn ($img) => [
                'url' => asset('storage/'.$img->path),
                'alt' => $img->alt_text,
                'is_primary' => $img->is_primary,
            ])->values()->all(),
            'variants' => $this->variants->map(fn ($v) => [
                'id' => $v->id,
                'size' => $v->size,
                'price' => (float) $v->price,
            ])->values()->all(),
            'from_price' => (float) ($this->variants->min('price') ?? 0),
        ];
    }
}
