<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleGarment extends Model
{
    protected $fillable = [
        'bundle_id', 'category_slug', 'qty', 'label', 'size_guide_key', 'sort_order',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }
}
