<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeGuide extends Model
{
    protected $fillable = [
        'key', 'code', 'name', 'note', 'sizes', 'rows', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'sizes' => 'array',
        'rows' => 'array',
        'is_active' => 'boolean',
    ];

    public function toFrontend(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'note' => $this->note,
            'sizes' => $this->sizes,
            'rows' => $this->rows,
        ];
    }
}
