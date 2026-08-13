<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductQuote extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'product_slug', 'product_title', 'product_brand',
        'product_style_code', 'product_image', 'colour_name', 'colour_hex', 'method',
        'view', 'placements', 'texts', 'size_breakdown', 'quantity',
        'garment_unit', 'branding_unit', 'text_unit', 'setup_fee', 'unit_price', 'subtotal',
        'preview_path', 'logo_path', 'logo_name', 'notes',
    ];

    protected $casts = [
        'placements' => 'array',
        'texts' => 'array',
        'size_breakdown' => 'array',
        'garment_unit' => 'float',
        'branding_unit' => 'float',
        'text_unit' => 'float',
        'setup_fee' => 'float',
        'unit_price' => 'float',
        'subtotal' => 'float',
    ];

    protected $appends = [
        'preview_url',
        'logo_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPreviewUrlAttribute(): ?string
    {
        // Prefer product photo for list/detail reliability; composite preview is secondary.
        if ($this->product_image) {
            return $this->product_image;
        }

        if ($this->preview_path) {
            return asset('storage/'.$this->preview_path);
        }

        return null;
    }

    public function getCompositePreviewUrlAttribute(): ?string
    {
        if (! $this->preview_path) {
            return null;
        }

        return asset('storage/'.$this->preview_path);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return asset('storage/'.$this->logo_path);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'PQ-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function methodLabel(): string
    {
        return $this->method === 'print' ? 'DTF / Print' : 'Embroidery';
    }
}
