<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\GroupGoal;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'timezone',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_preferences' => 'array',
    ];

    /**
     * Relation avec les objectifs.
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Relation avec les badges (many-to-many avec table pivot badge_user).
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class)->withPivot('earned_at')->withTimestamps();

    }

    /**
     * Accès aux journaux via les objectifs (hasManyThrough).
     */
    public function journals()
    {
        return $this->hasManyThrough(Journal::class, Goal::class);
    }
    public function groupGoals()
{
    return $this->belongsToMany(GroupGoal::class)->withPivot('progress', 'is_admin')->withTimestamps();
}
public function createdGroupGoals()
    {
        return $this->hasMany(GroupGoal::class, 'creator_id');
    }

}
