<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolProductVariant extends Model
{
    protected $fillable = ['school_product_id', 'size', 'price', 'sort_order'];

    protected $casts = ['price' => 'float'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SchoolProduct::class, 'school_product_id');
    }
}
