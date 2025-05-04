<?php

namespace App\Notifications;

use App\Models\GroupGoal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GroupGoalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $groupGoal;
    private $type;
    private $actor;
    private $message;

    public function __construct(GroupGoal $groupGoal, string $type, User $actor, string $message)
    {
        $this->groupGoal = $groupGoal;
        $this->type = $type;
        $this->actor = $actor;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'group_goal_id' => $this->groupGoal->id,
            'group_goal_title' => $this->groupGoal->title,
            'type' => $this->type,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'message' => $this->message
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'group_goal_id' => $this->groupGoal->id,
            'group_goal_title' => $this->groupGoal->title,
            'type' => $this->type,
            'actor_name' => $this->actor->name,
            'message' => $this->message,
            'time' => now()->diffForHumans()
        ]);
    }
}