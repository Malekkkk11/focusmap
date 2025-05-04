<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Badge;

class BadgeNotification extends Component
{
    public $badge;

    public function __construct(Badge $badge)
    {
        $this->badge = $badge;
    }

    public function render()
    {
        return view('components.badge-notification');
    }
}