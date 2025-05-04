<?php

namespace App\Policies;

use App\Models\Step;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StepPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Step $step)
    {
        return $user->id === $step->goal->user_id;
    }
}
