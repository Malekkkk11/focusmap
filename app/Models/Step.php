<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    protected $fillable = [
        'title',
        'description',
        'order',
        'completed',
        'due_date'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
        'order' => 'integer'
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}