<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Goal $goal)
    {
        dd([
            'auth_id' => auth()->id(),
            'goal_user_id' => $goal->user_id,
        ]);
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $order = $goal->steps()->max('order') + 1;

        $goal->steps()->create([
            ...$validated,
            'order' => $order,
            'completed' => false
        ]);

        return back()->with('success', 'Step added successfully.');
    }

    public function update(Request $request, Step $step)
    {
        $step->loadMissing('goal'); // <- Correction cruciale
        $this->authorize('update', $step);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
        ]);

        $step->update($validated);

        // Recalculer le progrès
        if (isset($validated['completed'])) {
            $total = $step->goal->steps()->count();
            $completed = $step->goal->steps()->where('completed', true)->count();
            $progress = $total > 0 ? ($completed / $total) * 100 : 0;
            $step->goal->update(['progress' => $progress]);
        }

        return back()->with('success', 'Step updated successfully.');
    }

    public function destroy(Step $step)
    {
        $step->loadMissing('goal'); // <- Correction cruciale
        $this->authorize('update', $step);

        $order = $step->order;
        $goal = $step->goal;

        $step->delete();

        $goal->steps()->where('order', '>', $order)->decrement('order');

        return back()->with('success', 'Step deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'steps' => 'required|array',
            'steps.*.id' => 'required|exists:steps,id',
            'steps.*.order' => 'required|integer|min:0'
        ]);

        foreach ($validated['steps'] as $stepData) {
            $step = Step::find($stepData['id']);
            $step->loadMissing('goal'); // <- Correction cruciale
            $this->authorize('update', $step);
            $step->update(['order' => $stepData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
