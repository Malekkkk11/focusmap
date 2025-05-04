<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Services\OpenAIService;
use App\Services\BadgeService;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->middleware('auth');
        $this->badgeService = $badgeService;
    }

    public function index()
    {
        $goals = auth()->user()->goals()->with('steps')->get();
        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'deadline' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
            'is_public' => 'boolean'
        ]);

        $goal = auth()->user()->goals()->create($validated);

        // Generate AI step suggestions if OpenAI is configured
        if (config('services.openai.api_key')) {
            try {
                $openAI = new OpenAIService();
                $suggestions = $openAI->generateStepSuggestions($goal->title, $goal->description ?? '');
                
                foreach ($suggestions as $index => $suggestion) {
                    $goal->steps()->create([
                        'title' => $suggestion['title'],
                        'description' => $suggestion['description'],
                        'order' => $index
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to generate step suggestions: ' . $e->getMessage());
            }
        }

        // Check for new badges
        $earnedBadges = $this->badgeService->checkAndAwardBadges(auth()->user()) ?? [];

return redirect()->route('goals.show', $goal)
    ->with('success', 'Goal created successfully!' .
        (count($earnedBadges) ? ' You earned new badges!' : ''));

    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        $goal->load(['steps', 'journals']);
        return view('goals.show', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'deadline' => 'nullable|date',
            'progress' => 'nullable|numeric|min:0|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
            'is_public' => 'boolean',
            
        ]);

        $goal->update($validated);

        // If goal is completed, check for badges
        if ($validated['progress'] == 100) {
            $earnedBadges = $this->badgeService->checkAndAwardBadges(auth()->user());
            if (count($earnedBadges) > 0) {
                session()->flash('badge_alert', 'You earned new badges!');
            }
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Goal updated successfully!');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->delete();
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully!');
    }

    public function map()
    {
        $goals = auth()->user()->goals()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        return view('goals.map', compact('goals'));
    }

    public function mindmap()
    {
        $goals = auth()->user()->goals()
            ->with('steps')
            ->get();
        return view('goals.mindmap', compact('goals'));
    }
    public function edit(Goal $goal)
    {
        return view('goals.edit', compact('goal'));
    }
    

}