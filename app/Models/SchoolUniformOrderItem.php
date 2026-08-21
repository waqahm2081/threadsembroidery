<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolUniformOrderItem extends Model
{
    protected $fillable = [
        'school_uniform_order_id', 'school_product_id', 'school_product_variant_id',
        'product_name', 'size', 'quantity', 'unit_price', 'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'line_total' => 'float',
    ];

    public function order(): BelongsTo { return $this->belongsTo(SchoolUniformOrder::class, 'school_uniform_order_id'); }
    public function product(): BelongsTo { return $this->belongsTo(SchoolProduct::class, 'school_product_id'); }
    public function variant(): BelongsTo { return $this->belongsTo(SchoolProductVariant::class, 'school_product_variant_id'); }
}
