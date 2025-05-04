<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = auth()->user()->load(['goals', 'badges']);
        $recentActivity = $this->getRecentActivity($user);

        $goals = $user->goals;

        $totalGoals = $goals->count();
        $completedGoals = $goals->where('progress', 100)->count();
        $uniqueCategories = $goals->pluck('category')->unique()->count();
        $completionRate = $totalGoals > 0 
            ? ($completedGoals / $totalGoals) * 100 
            : 0;

            return view('profile.show', [
                'user' => $user,
                'totalGoals' => $totalGoals,
                'completedGoals' => $completedGoals,
                'uniqueCategories' => $uniqueCategories,
                'completionRate' => $completionRate,
                'recentActivity' => $recentActivity
            ]);
            
    }

    public function settings()
    {
        $user = auth()->user();
        return view('profile.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'timezone' => 'required|string',
            'notification_preferences' => 'array'
        ]);

        $user = auth()->user();
        $user->update($request->only([
            'name',
            'email',
            'timezone',
            'notification_preferences'
        ]));

        return redirect()->route('profile.settings')
            ->with('success', 'Profile settings updated successfully');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = asset('storage/' . $avatarPath);
            $user->save();
        }

        return back()->with('success', 'Profile picture updated successfully');
    }

    private function getRecentActivity($user)
    {
        $activity = collect();

        // Badges
        $user->badges->each(function ($badge) use (&$activity) {
            $activity->push([
                'type' => 'badge',
                'description' => "Earned the {$badge->name} badge",
                'time' => $badge->pivot->earned_at
            ]);
        });

        // Completed goals
        $user->goals->where('progress', 100)->each(function ($goal) use (&$activity) {
            $activity->push([
                'type' => 'goal',
                'description' => "Completed goal: {$goal->title}",
                'time' => $goal->updated_at
            ]);
        });

        // Journals
        $user->goals->each(function ($goal) use (&$activity) {
            if (method_exists($goal, 'journals')) {
                $goal->journals->each(function ($journal) use (&$activity, $goal) {
                    $activity->push([
                        'type' => 'journal',
                        'description' => "Added journal entry for {$goal->title}",
                        'time' => $journal->created_at
                    ]);
                });
            }
        });

        return $activity->sortByDesc('time')->take(10);
    }
    public function destroy(Request $request)
{
    $user = $request->user();
    $user->delete();

    auth()->logout();

    return redirect('/')->with('success', 'Your profile has been deleted.');
}
    
}
