<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'item_type', 'bundle_id', 'title', 'image_path',
        'colour_name', 'colour_hex', 'method', 'size_breakdown', 'garments_snapshot',
        'addons', 'quantity', 'base_price', 'addon_total', 'line_total', 'summary',
    ];

    protected $casts = [
        'size_breakdown' => 'array',
        'garments_snapshot' => 'array',
        'addons' => 'array',
        'quantity' => 'integer',
        'base_price' => 'float',
        'addon_total' => 'float',
        'line_total' => 'float',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CartItemFile::class);
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image_path ? asset('storage/'.$this->image_path) : asset('assets/img/logo.png'),
            'summary' => $this->summary,
            'method' => $this->method,
            'totalQty' => $this->quantity,
            'total' => (float) $this->line_total,
            'base_price' => (float) $this->base_price,
            'addon_total' => (float) $this->addon_total,
            'colour' => $this->colour_name,
            'addons' => $this->addons ?? [],
            'garments' => $this->garments_snapshot ?? [],
            'sizes' => $this->size_breakdown ?? [],
            'files' => $this->files->map(fn (CartItemFile $f) => [
                'placement' => $f->placement_slug,
                'label' => $f->placement_label,
                'name' => $f->original_name,
                'url' => asset('storage/'.$f->path),
            ])->values()->all(),
        ];
    }
}
