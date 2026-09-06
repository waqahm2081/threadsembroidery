<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function colours(): BelongsToMany
    {
        return $this->belongsToMany(BundleColour::class, 'bundle_garment_colour')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order')
            ->orderBy('bundle_colours.sort_order');
    }
}
