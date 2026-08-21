<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo_path', 'contact_email', 'contact_phone',
        'address', 'description', 'accent_colour', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function categories(): HasMany
    {
        return $this->hasMany(SchoolProductCategory::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function allCategories(): HasMany
    {
        return $this->hasMany(SchoolProductCategory::class)->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(SchoolProduct::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function allProducts(): HasMany
    {
        return $this->hasMany(SchoolProduct::class)->orderBy('sort_order');
    }

    public function uniformOrders(): HasMany
    {
        return $this->hasMany(SchoolUniformOrder::class);
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $this->logo_url,
            'description' => $this->description,
            'accent_colour' => $this->accent_colour ?: '#e85820',
            'contact_email' => $this->contact_email,
        ];
    }
}
