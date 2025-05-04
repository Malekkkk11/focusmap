<?php

namespace App\Notifications;

use App\Models\Badge;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BadgeEarned extends Notification implements ShouldQueue
{
    use Queueable;

    private $badge;

    public function __construct(Badge $badge)
    {
        $this->badge = $badge;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'badge_id' => $this->badge->id,
            'badge_name' => $this->badge->name,
            'badge_description' => $this->badge->description,
            'badge_icon' => $this->badge->icon,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'badge' => [
                'name' => $this->badge->name,
                'description' => $this->badge->description,
                'icon' => $this->badge->icon,
            ]
        ]);
    }
}