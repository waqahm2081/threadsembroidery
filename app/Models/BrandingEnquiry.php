<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BrandingEnquiry extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'full_name',
        'business_name',
        'email',
        'whatsapp',
        'garments',
        'colours',
        'placements',
        'branding_method',
        'min_order_confirmed',
        'status',
        'admin_notes',
        'quoted_amount',
    ];

    protected $casts = [
        'placements' => 'array',
        'min_order_confirmed' => 'boolean',
        'quoted_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(BrandingEnquiryFile::class);
    }

    public static function generateReference(): string
    {
        $year = now()->format('Y');
        $prefix = "BE-{$year}-";

        $latest = static::query()
            ->where('reference', 'like', $prefix.'%')
            ->orderByDesc('reference')
            ->value('reference');

        $sequence = 1;

        if ($latest && preg_match('/BE-\d{4}-(\d+)$/', $latest, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }

    public function displayStatus(): string
    {
        return match ($this->status) {
            'submitted' => 'Submitted',
            'review' => 'Under Review',
            'quoted' => 'Quote Sent',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function filterStatus(): string
    {
        return match ($this->status) {
            'submitted' => 'submitted',
            'review' => 'review',
            'quoted' => 'quoted',
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => $this->status,
        };
    }

    public function serviceSummary(): string
    {
        $placements = collect($this->placements ?? [])->take(2)->implode(', ');
        $method = match ($this->branding_method) {
            'embroidery' => 'Embroidery',
            'dtf' => 'DTF Print',
            'both' => 'Embroidery & DTF',
            'not_sure' => 'Method TBC',
            default => ucfirst(str_replace('_', ' ', $this->branding_method)),
        };

        if ($placements === '') {
            return $method;
        }

        return $placements.' — '.$method;
    }

    public function garmentsSummary(): string
    {
        $text = trim($this->garments);

        return Str::limit($text, 60);
    }
}
