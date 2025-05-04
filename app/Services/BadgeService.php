<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\Journal;
use App\Notifications\BadgeEarned;
use Carbon\Carbon;

class BadgeService
{
    public function checkAndAwardBadges(User $user)
    {
        $this->checkStreakBadges($user);
        $this->checkJournalBadges($user);
        $this->checkActivityBadges($user);
        $this->checkGroupBadges($user);
    }

    private function checkStreakBadges(User $user)
    {
        $streak = $this->calculateCurrentStreak($user);
        
        $streakBadges = [
            7 => 'Weekly Warrior',
            30 => 'Monthly Master',
            100 => 'Centurion',
        ];

        foreach ($streakBadges as $days => $badgeName) {
            if ($streak >= $days) {
                $this->awardBadge($user, $badgeName);
            }
        }
    }

    private function calculateCurrentStreak(User $user)
    {
        $journals = Journal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($journals->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $lastDate = Carbon::now()->startOfDay();
        $firstEntry = true;

        foreach ($journals as $journal) {
            $entryDate = Carbon::parse($journal->created_at)->startOfDay();
            
            if ($firstEntry) {
                if ($lastDate->diffInDays($entryDate) > 1) {
                    return 0;
                }
                $firstEntry = false;
            }

            if ($lastDate->diffInDays($entryDate) <= 1) {
                $streak++;
                $lastDate = $entryDate;
            } else {
                break;
            }
        }

        return $streak;
    }

    private function checkJournalBadges(User $user)
    {
        $journalCount = Journal::where('user_id', $user->id)->count();
        
        $journalBadges = [
            1 => 'First Entry',
            10 => 'Dedicated Journalist',
            50 => 'Reflection Master',
        ];

        foreach ($journalBadges as $count => $badgeName) {
            if ($journalCount >= $count) {
                $this->awardBadge($user, $badgeName);
            }
        }
    }

    private function checkActivityBadges(User $user)
    {
        $lastWeekEntries = Journal::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();

        if ($lastWeekEntries >= 5) {
            $this->awardBadge($user, 'Active Participant');
        }
    }

    private function checkGroupBadges(User $user)
    {
        // Check for group participation badges
        $participatingCount = $user->groupGoals()->count();
        $completedCount = $user->groupGoals()
            ->wherePivot('progress', 100)
            ->count();
        
        $participationBadges = [
            1 => 'Team Player',
            5 => 'Group Enthusiast',
            10 => 'Community Champion'
        ];

        foreach ($participationBadges as $count => $badgeName) {
            if ($participatingCount >= $count) {
                $this->awardBadge($user, $badgeName);
            }
        }

        // Check for group completion badges
        $completionBadges = [
            1 => 'Group Achiever',
            5 => 'Group Master',
            10 => 'Group Legend'
        ];

        foreach ($completionBadges as $count => $badgeName) {
            if ($completedCount >= $count) {
                $this->awardBadge($user, $badgeName);
            }
        }

        // Check for group creation badges
        $createdCount = $user->createdGroupGoals()->count();
        $creatorBadges = [
            1 => 'Group Leader',
            3 => 'Community Builder',
            5 => 'Inspiration Leader'
        ];

        foreach ($creatorBadges as $count => $badgeName) {
            if ($createdCount >= $count) {
                $this->awardBadge($user, $badgeName);
            }
        }
    }

    private function awardBadge(User $user, string $badgeName)
    {
        $badge = Badge::firstOrCreate(['name' => $badgeName]);
        
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id);
            $user->notify(new BadgeEarned($badge));
        }
    }
}