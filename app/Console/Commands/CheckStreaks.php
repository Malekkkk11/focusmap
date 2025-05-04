<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class CheckStreaks extends Command
{
    protected $signature = 'badges:check-streaks';
    protected $description = 'Check and update user badges based on activity streaks';

    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        parent::__construct();
        $this->badgeService = $badgeService;
    }

    public function handle()
    {
        $this->info('Starting streak checks...');

        $users = User::all();
        $badgesAwarded = 0;

        foreach ($users as $user) {
            $newBadges = $this->badgeService->checkAndAwardBadges($user);
            $badgesAwarded += count($newBadges);

            if (count($newBadges) > 0) {
                $this->info("Awarded {$user->name} " . count($newBadges) . " new badge(s)");
            }
        }

        $this->info("Streak check completed. Total badges awarded: {$badgesAwarded}");
        return Command::SUCCESS;
    }
}