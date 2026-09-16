<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductBulkEnquiry extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'full_name',
        'email',
        'phone',
        'company_name',
        'product_title',
        'product_style_code',
        'category_slug',
        'product_slug',
        'colour_name',
        'colour_code',
        'colour_hex',
        'size_breakdown',
        'quantity',
        'decoration_methods',
        'logo_setup',
        'estimated_unit_price',
        'estimated_subtotal',
        'message',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'size_breakdown' => 'array',
        'decoration_methods' => 'array',
        'logo_setup' => 'boolean',
        'estimated_unit_price' => 'float',
        'estimated_subtotal' => 'float',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'PBE-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function displayStatus(): string
    {
        return match ($this->status) {
            'submitted' => 'Submitted',
            'review' => 'Under Review',
            'quoted' => 'Quote Sent',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function sizeBreakdownLabel(): string
    {
        $rows = $this->size_breakdown ?? [];
        if (! $rows) {
            return '—';
        }

        return collect($rows)
            ->map(fn ($r) => ($r['size'] ?? '').' × '.($r['qty'] ?? 0))
            ->filter()
            ->implode(', ');
    }

    public function methodsLabel(): string
    {
        $methods = $this->decoration_methods ?? [];
        if (! $methods) {
            return '—';
        }

        return collect($methods)
            ->map(fn ($m) => $m === 'embroidery' ? 'Embroidery' : 'Print')
            ->implode(', ');
    }
}
