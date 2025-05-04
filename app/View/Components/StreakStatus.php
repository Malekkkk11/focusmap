<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Carbon\Carbon;

class StreakStatus extends Component
{
    public $journalStreak;
    public $activeGoals;
    public $weeklyProgress;
    public $nextBadges;

    public function __construct()
    {
        $user = auth()->user();
        
        $this->journalStreak = $this->calculateJournalStreak($user);
        $this->activeGoals = $this->calculateActiveGoals($user);
        $this->weeklyProgress = $this->calculateWeeklyProgress($user);
        $this->nextBadges = $this->getNextBadges($user);
    }

    private function calculateJournalStreak($user)
    {
        $journals = $user->goals()
            ->with('journals')
            ->get()
            ->pluck('journals')
            ->flatten()
            ->sortBy('created_at');

        if ($journals->isEmpty()) {
            return 0;
        }

        $currentStreak = 0;
        $lastEntry = null;
        $today = Carbon::today();

        foreach ($journals->reverse() as $journal) {
            $entryDate = Carbon::parse($journal->created_at)->startOfDay();
            
            if (!$lastEntry) {
                if ($entryDate->diffInDays($today) > 1) {
                    return 0;
                }
                $currentStreak = 1;
            } else {
                if ($entryDate->diffInDays($lastEntry) > 1) {
                    break;
                }
                $currentStreak++;
            }
            
            $lastEntry = $entryDate;
        }

        return $currentStreak;
    }

    private function calculateActiveGoals($user)
    {
        return $user->goals()
            ->where('progress', '<', 100)
            ->where('progress', '>', 0)
            ->count();
    }

    private function calculateWeeklyProgress($user)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $goals = $user->goals()
            ->where('updated_at', '>=', $startOfWeek)
            ->get();

        if ($goals->isEmpty()) {
            return 0;
        }

        $totalProgress = $goals->sum('progress');
        $maxPossibleProgress = $goals->count() * 100;

        return $maxPossibleProgress > 0 
            ? round(($totalProgress / $maxPossibleProgress) * 100) 
            : 0;
    }

    private function getNextBadges($user)
    {
        $nextBadges = [];
        
        // Journal streak badge progress
        $streakMilestones = [3, 7, 30];
        $currentStreak = $this->journalStreak;
        foreach ($streakMilestones as $milestone) {
            if ($currentStreak < $milestone) {
                $nextBadges[] = [
                    'name' => $this->getStreakBadgeName($milestone),
                    'description' => "Maintain a {$milestone}-day journal streak",
                    'icon' => 'calendar-check',
                    'progress' => $currentStreak,
                    'required' => $milestone
                ];
                break;
            }
        }

        // Goal completion rate badge progress
        $totalGoals = $user->goals()->count();
        $completedGoals = $user->goals()->where('progress', 100)->count();
        $completionRate = $totalGoals > 0 ? ($completedGoals / $totalGoals) * 100 : 0;
        
        $rateMilestones = [25, 50, 75, 100];
        foreach ($rateMilestones as $milestone) {
            if ($completionRate < $milestone) {
                $nextBadges[] = [
                    'name' => $this->getCompletionBadgeName($milestone),
                    
                        'description' => 'Complete ' . $milestone . '% of your goals',
                        'icon' => 'trophy',
                        'progress' => round($completionRate),
                        'required' => $milestone
                    
                    
                ];
                break;
            }
        }

        return $nextBadges;
    }

    private function getStreakBadgeName($days)
{
    if ($days === 7) {
        return 'Week Warrior';
    } elseif ($days === 30) {
        return 'Monthly Master';
    } else {
        return 'Streak Master';
    }
}

    private function getCompletionBadgeName($rate)
    {
        if ($rate === 25) {
            return 'Getting There';
        } elseif ($rate === 50) {
            return 'Halfway There';
        } elseif ($rate === 75) {
            return 'Almost There';
        } elseif ($rate === 100) {
            return 'Perfectionist';
        } else {
            return 'Goal Achiever';
        }
    }

    public function render()
    {
        return view('components.streak-status');
    }
}