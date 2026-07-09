<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandingEnquiryFile extends Model
{
    protected $fillable = [
        'branding_enquiry_id',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(BrandingEnquiry::class, 'branding_enquiry_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }
}
