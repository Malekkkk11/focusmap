<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Services\BadgeService;

class JournalController extends Controller
{
    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->middleware('auth');
        $this->badgeService = $badgeService;
    }

    public function store(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'content' => 'required|string',
            'mood' => 'nullable|string|in:happy,neutral,sad',
            'progress_update' => 'nullable|numeric|min:0|max:100'
        ]);

        $journal = $goal->journals()->create($validated);

        // Check for badges after adding journal entry
        $this->badgeService->checkAndAwardBadges(auth()->user());

        return back()->with('success', 'Journal entry added successfully.');
    }

    public function update(Request $request, Journal $journal)
    {
        $this->authorize('update', $journal->goal);

        $validated = $request->validate([
            'content' => 'required|string',
            'mood' => 'nullable|string|in:happy,neutral,sad',
            'progress_update' => 'nullable|numeric|min:0|max:100'
        ]);

        $journal->update($validated);

        return back()->with('success', 'Journal entry updated successfully.');
    }

    public function destroy(Journal $journal)
    {
        $this->authorize('update', $journal->goal);
        
        $journal->delete();
        
        return back()->with('success', 'Journal entry deleted successfully.');
    }

    public function show(Journal $journal)
    {
        $this->authorize('view', $journal->goal);
        
        return response()->json($journal);
    }
}