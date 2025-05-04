<?php

namespace App\Http\Controllers;

use App\Models\GroupGoal;
use Illuminate\Http\Request;
use App\Services\BadgeService;
use App\Notifications\GroupGoalNotification;

class GroupGoalController extends Controller
{
    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->middleware('auth');
        $this->badgeService = $badgeService;
    }

    public function index()
    {
        $participatingGoals = auth()->user()->groupGoals()
            ->with('creator', 'participants')
            ->get();

        $availableGoals = GroupGoal::whereDoesntHave('participants', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->where(function($query) {
                $query->whereNull('participants_limit')
                    ->orWhereRaw('(SELECT COUNT(*) FROM group_goal_user WHERE group_goal_id = group_goals.id) < participants_limit');
            })
            ->with('creator', 'participants')
            ->get();

        return view('group_goals.index', compact('participatingGoals', 'availableGoals'));
    }

    public function create()
    {
        return view('group_goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'participants_limit' => 'nullable|integer|min:2'
        ]);

        $groupGoal = GroupGoal::create([
            ...$validated,
            'creator_id' => auth()->id()
        ]);

        // Add creator as admin participant
        $groupGoal->participants()->attach(auth()->id(), [
            'is_admin' => true,
            'progress' => 0
        ]);

        // Notify creator of successful creation
        auth()->user()->notify(new GroupGoalNotification(
            $groupGoal,
            'created',
            auth()->user(),
            'You created a new group goal'
        ));

        return redirect()->route('group-goals.show', $groupGoal)
            ->with('success', 'Group goal created successfully!');
    }

    public function show(GroupGoal $groupGoal)
    {
        $groupGoal->load(['creator', 'participants']);
        return view('group_goals.show', compact('groupGoal'));
    }

    public function join(GroupGoal $groupGoal)
    {
        if ($groupGoal->userIsParticipant(auth()->user())) {
            return back()->with('error', 'You are already participating in this goal.');
        }

        if (!$groupGoal->hasAvailableSpots()) {
            return back()->with('error', 'This group goal has reached its participants limit.');
        }

        $groupGoal->participants()->attach(auth()->id(), [
            'progress' => 0
        ]);

        // Notify other participants
        foreach ($groupGoal->participants as $participant) {
            if ($participant->id !== auth()->id()) {
                $participant->notify(new GroupGoalNotification(
                    $groupGoal,
                    'joined',
                    auth()->user(),
                    auth()->user()->name . ' joined the group goal'
                ));
            }
        }

        return redirect()->route('group-goals.show', $groupGoal)
            ->with('success', 'You have joined the group goal!');
    }

    public function leave(GroupGoal $groupGoal)
    {
        if (!$groupGoal->userIsParticipant(auth()->user())) {
            return back()->with('error', 'You are not participating in this goal.');
        }

        if ($groupGoal->userIsAdmin(auth()->user()) && $groupGoal->participants()->where('is_admin', true)->count() === 1) {
            return back()->with('error', 'You cannot leave as you are the only admin.');
        }

        $groupGoal->participants()->detach(auth()->id());

        // Notify other participants
        foreach ($groupGoal->participants as $participant) {
            $participant->notify(new GroupGoalNotification(
                $groupGoal,
                'left',
                auth()->user(),
                auth()->user()->name . ' left the group goal'
            ));
        }

        return redirect()->route('group-goals.index')
            ->with('success', 'You have left the group goal.');
    }

    public function updateProgress(GroupGoal $groupGoal, Request $request)
    {
        if (!$groupGoal->userIsParticipant(auth()->user())) {
            return back()->with('error', 'You are not participating in this goal.');
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $oldProgress = $groupGoal->participants()->where('user_id', auth()->id())->first()->pivot->progress;
        $groupGoal->participants()->updateExistingPivot(auth()->id(), [
            'progress' => $validated['progress']
        ]);

        // Notify other participants of significant progress changes
        if ($validated['progress'] > $oldProgress) {
            foreach ($groupGoal->participants as $participant) {
                if ($participant->id !== auth()->id()) {
                    $participant->notify(new GroupGoalNotification(
                        $groupGoal,
                        'progress',
                        auth()->user(),
                        auth()->user()->name . ' updated their progress to ' . $validated['progress'] . '%'
                    ));
                }
            }
        }

        // Check for badges if progress is 100%
        if ($validated['progress'] === 100) {
            $this->badgeService->checkAndAwardBadges(auth()->user());

            // Notify others of completion
            foreach ($groupGoal->participants as $participant) {
                if ($participant->id !== auth()->id()) {
                    $participant->notify(new GroupGoalNotification(
                        $groupGoal,
                        'completed',
                        auth()->user(),
                        auth()->user()->name . ' completed their part of the group goal!'
                    ));
                }
            }
        }

        return back()->with('success', 'Progress updated successfully!');
    }
    public function groupGoals()
{
    return $this->belongsToMany(\App\Models\GroupGoal::class, 'group_goal_user')
                ->withPivot('progress', 'is_admin')
                ->withTimestamps();
}

}