<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'progress',
        'latitude',
        'longitude',
        'location_name',
        'deadline',
        'is_public',
        'progress',
    ];

    protected $casts = [
        'deadline' => 'date',
        'progress' => 'float',
        'is_public' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }
}
