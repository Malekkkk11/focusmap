<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupGoal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'start_date',
        'end_date',
        'participants_limit',
        'creator_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_goal_user')
            ->withPivot('progress', 'is_admin')
            ->withTimestamps();
    }

    public function getAverageProgressAttribute(): float
    {
        return $this->participants()->avg('progress') ?? 0;
    }

    public function getParticipantsCountAttribute(): int
    {
        return $this->participants()->count();
    }

    public function hasAvailableSpots(): bool
    {
        if (!$this->participants_limit) {
            return true;
        }
        return $this->participants_count < $this->participants_limit;
    }

    public function userIsParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function userIsAdmin(User $user): bool
    {
        return $this->participants()
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->exists();
    }
    public function users()
{
    return $this->belongsToMany(\App\Models\User::class, 'group_goal_user')
                ->withPivot('progress', 'is_admin')
                ->withTimestamps();
}
}