<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Http\Request;

class PublicGoalController extends Controller
{
    public function index(Request $request)
    {
        $query = Goal::where('is_public', true)
            ->with('user');

        // Apply category filter
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Apply sorting
        switch ($request->get('sort', 'Latest')) {
            case 'Popular':
                $query->withCount('journals')
                    ->orderByDesc('journals_count');
                break;
            case 'Progress':
                $query->orderByDesc('progress');
                break;
            default: // Latest
                $query->latest();
                break;
        }

        $goals = $query->paginate(12);

        // Get categories with counts
        $categories = Goal::where('is_public', true)
            ->select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');
            $activity->push([
                'type' => 'badge',
                'description' => "Earned the {$badge->name} badge",
                'time' => $badge->pivot->earned_at,
            ]);
            

        // Get popular users
        $popularUsers = User::withCount(['goals' => function($query) {
                $query->where('is_public', true);
            }])
            ->having('goals_count', '>', 0)
            ->orderByDesc('goals_count')
            ->take(5)
            ->get()
            ->map(function($user) {
                $user->public_goals_count = $user->goals_count;
                $user->total_progress = $user->goals()
                    ->where('is_public', true)
                    ->avg('progress') ?? 0;
                return $user;
            });

        return view('goals.public', compact('goals', 'categories', 'popularUsers'));
    }
}