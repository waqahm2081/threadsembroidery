<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserArtwork extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'original_name',
        'path',
        'source_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }

    public function typeLabel(): string
    {
        return strtoupper(pathinfo($this->original_name ?: $this->path, PATHINFO_EXTENSION) ?: 'FILE');
    }

    public function toFrontend(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->typeLabel(),
            'url' => $this->url(),
        ];
    }
}
