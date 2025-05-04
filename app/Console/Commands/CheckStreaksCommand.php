<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class CheckStreaksCommand extends Command
{
    protected $signature = 'streaks:check';
    protected $description = 'Check and update user streaks and award badges';

    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        parent::__construct();
        $this->badgeService = $badgeService;
    }

    public function handle()
    {
        $this->info('Starting streak checks...');
        
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->badgeService->checkAndAwardBadges($user);
            }
        });

        $this->info('Streak checks completed successfully.');
    }
}