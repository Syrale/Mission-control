<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Load Data (SCOPED TO USER)
        $games = Game::where('user_id', auth()->id())
                     ->with(['tasks', 'events', 'maintenances'])
                     ->get();

        // 2. Run Reset Logic
        $games->each(function ($game) {
            $game->tasks->each->checkReset();
        });

        // 3. Calculate Variables
        $now = now();

        // A. Active Maintenance (Happening Now)
        $activeMaintenances = $games->flatMap->maintenances->filter(function ($m) use ($now) {
            return $now->between($m->start_at, $m->end_at);
        });
        
        // B. Recently Finished (Ended in the last 24 hours)
        $finishedMaintenances = $games->flatMap->maintenances->filter(function ($m) use ($now) {
            return $m->end_at->isPast() && $m->end_at->diffInHours($now) < 24;
        });

        // C. Get ALL incomplete tasks
        $allPendingTasks = $games->flatMap->tasks->filter(function ($t) {
            return !$t->is_completed;
        });

        // D. Missed Tasks (Overdue)
        $missedTasks = $allPendingTasks->filter(function ($t) use ($now) {
            return $t->next_due_at && $t->next_due_at->isPast();
        })->sortBy('next_due_at');

        return view('dashboard', [
            'games' => $games,
            'activeMaintenances' => $activeMaintenances,
            'finishedMaintenances' => $finishedMaintenances, 
            'todoTasks' => $allPendingTasks, 
            'missedTasks' => $missedTasks,
        ]);
    }
}