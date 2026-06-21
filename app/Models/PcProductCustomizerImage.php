<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcProductCustomizerImage extends Model
{
    protected $table = 'pc_product_customizer_images';

    protected $fillable = [
        'product_id',
        'image',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(PcProduct::class, 'product_id', 'id');
    }
}
