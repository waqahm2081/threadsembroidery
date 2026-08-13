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
        'company_name',
        'email',
        'phone',
        'enquiry_type',
        'quantity',
        'branding_method',
        'placements',
        'message',
        'status',
        'admin_notes',
        'quoted_amount',
    ];

    protected $casts = [
        'placements' => 'array',
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
        do {
            $reference = 'BE-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
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
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function statusKey(): string
    {
        return match ($this->status) {
            'submitted', 'review' => 'review',
            'quoted' => 'quoted',
            'in_progress' => 'production',
            'completed' => 'delivered',
            'cancelled' => 'cancelled',
            default => 'review',
        };
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->id,
            'ref' => $this->reference,
            'status' => $this->displayStatus(),
            'status_key' => $this->statusKey(),
            'date' => optional($this->created_at)->format('d M Y'),
            'enquiry_type' => $this->enquiry_type,
            'quantity' => $this->quantity,
            'method' => $this->branding_method,
            'placements' => $this->placements ?? [],
            'message' => $this->message,
            'company_name' => $this->company_name,
            'quoted_amount' => $this->quoted_amount,
            'files' => $this->files->map(fn (BrandingEnquiryFile $f) => [
                'name' => $f->original_name,
                'url' => $f->url,
                'type' => strtoupper(pathinfo($f->original_name ?: $f->file_path, PATHINFO_EXTENSION) ?: 'FILE'),
            ])->values()->all(),
        ];
    }
}
