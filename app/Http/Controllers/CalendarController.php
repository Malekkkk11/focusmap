<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $goals = auth()->user()->goals()
            ->whereNotNull('deadline')
            ->get();

        $calendarEvents = $goals->map(function ($goal) {
            return [
                'id' => $goal->id,
                'title' => $goal->title,
                'start' => $goal->deadline->format('Y-m-d'),
                'backgroundColor' => $this->getColorByProgress($goal->progress),
                'borderColor' => $this->getColorByProgress($goal->progress),
                'textColor' => '#fff',
            ];
        });

        $upcomingGoals = $goals->where('deadline', '>=', now())
            ->sortBy('deadline')
            ->take(5);

        return view('goals.calendar', compact('calendarEvents', 'upcomingGoals'));
    }

    private function getColorByProgress($progress)
    {
        if ($progress >= 100) return '#28a745'; // Completed
        if ($progress >= 75) return '#17a2b8';  // Almost there
        if ($progress >= 50) return '#ffc107';  // Halfway
        if ($progress >= 25) return '#fd7e14';  // Started
        return '#dc3545';                       // Just beginning
    }
}