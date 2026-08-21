<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolProductImage extends Model
{
    protected $fillable = ['school_product_id', 'path', 'alt_text', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SchoolProduct::class, 'school_product_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }
}
