<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolProductCategory extends Model
{
    protected $fillable = ['school_id', 'name', 'slug', 'description', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(SchoolProduct::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function allProducts(): HasMany
    {
        return $this->hasMany(SchoolProduct::class)->orderBy('sort_order');
    }
}
