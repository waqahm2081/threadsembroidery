<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleInclude extends Model
{
    protected $fillable = ['bundle_id', 'text', 'sort_order'];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }
}
