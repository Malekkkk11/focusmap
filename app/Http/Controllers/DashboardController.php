<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Get all user goals with their steps
        $goals = $user->goals()->with('steps')->get();
        
        // Calculate statistics
        $activeGoals = $goals->where('progress', '<', 100)->count();
        $completedSteps = $goals->sum(function($goal) {
            return $goal->steps->where('completed', true)->count();
        });
        $goalsWithLocation = $goals->whereNotNull('latitude')->count();
        $goalsDueSoon = $goals->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(7))
            ->count();

        // Get recent goals
        $recentGoals = $goals->sortByDesc('updated_at')->take(5);

        // Calculate goals by category
        $goalsByCategory = $goals->groupBy('category')
            ->map->count();
        $totalGoals = $goals->count();

        // Get upcoming deadlines
        $upcomingDeadlines = $goals->where('deadline', '>=', now())
            ->sortBy('deadline')
            ->take(5);

        return view('dashboard', compact(
            'activeGoals',
            'completedSteps',
            'goalsWithLocation',
            'goalsDueSoon',
            'recentGoals',
            'goalsByCategory',
            'totalGoals',
            'upcomingDeadlines'
        ));
    }
}