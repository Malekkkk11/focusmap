<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = [
        'content',
        'mood',
        'progress_update'
    ];

    protected $casts = [
        'progress_update' => 'float'
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}