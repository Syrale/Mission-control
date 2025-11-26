<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Game;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = now();

        // 1. FETCH DATA (Use your original Scoped Logic)
        // We fetch games belonging to the user, with relations
        $games = Game::where('user_id', $user->id)
                     ->with(['events', 'maintenances', 'tasks'])
                     ->get();

        // 2. RUN RESET LOGIC (Keep this from your original code!)
        // This ensures 'next_due_at' is accurate before we do anything else
        $games->each(function ($game) {
            $game->tasks->each->checkReset();
        });

        // 3. GATHER ALL INCOMPLETE TASKS
        // Since tasks belong to games, we flatten the collection here
        $todoTasks = $games->flatMap->tasks->filter(function ($t) {
            return !$t->is_completed; // Assuming attribute is is_completed or completed
        });

        // 4. CALCULATE ACTIVE MAINTENANCES
        $activeMaintenances = $games->flatMap->maintenances->filter(function ($m) use ($now) {
            return $now->between($m->start_at, $m->end_at);
        });

        // 5. CALCULATE ALERTS (New Structure)
        $alerts = [
            'critical' => collect(),
            'urgent'   => collect(),
            'upcoming' => collect(),
        ];

        foreach ($games as $g) {
            foreach ($g->events as $e) {
                if ($e->end_time->isPast()) continue;
                $e->game_name = $g->name; 

                if ($e->end_time->lte($now->copy()->addHours(6))) {
                    $alerts['critical']->push($e);
                } elseif ($e->end_time->lte($now->copy()->addHours(24))) {
                    $alerts['urgent']->push($e);
                } elseif ($e->end_time->lte($now->copy()->addDays(3))) {
                    $alerts['upcoming']->push($e);
                }
            }
        }

        // 6. CALCULATE SORTED GAMES (New Structure for Grid/List)
        $sortedGames = $games->map(function ($game) use ($todoTasks, $activeMaintenances, $now) {
            // Filter tasks specific to this game
            $gameTasks = $todoTasks->where('game_id', $game->id);
            
            // Find earliest due task
            $earliestTask = $gameTasks->whereNotNull('next_due_at')->sortBy('next_due_at')->first();
            $minutesUntilDue = $earliestTask ? $now->diffInMinutes($earliestTask->next_due_at, false) : 999999;
            
            // Check maintenance
            $activeMaint = $activeMaintenances->where('game_id', $game->id)->first();
            $isMaintenance = $activeMaint !== null;

            // Calculate Score
            if ($isMaintenance) {
                $score = 0;
            } elseif ($gameTasks->isEmpty()) {
                $score = 4;
            } elseif ($minutesUntilDue < 180) {
                $score = 1;
            } elseif ($minutesUntilDue < 1440) {
                $score = 2;
            } else {
                $score = 3;
            }

            return (object) [
                'game' => $game,
                'score' => $score,
                'minutes' => $minutesUntilDue,
                'tasks' => $gameTasks,
                'isMaintenance' => $isMaintenance,
                'maintenance' => $activeMaint
            ];
        })->sortBy([['score', 'asc'], ['minutes', 'asc']]);

        // 7. CALCULATE SCHEDULE (New Structure for Calendar)
        $calendar = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $now->copy()->addDays($i);
            
            $dayTasks = $todoTasks->filter(fn($t) => $t->next_due_at && $t->next_due_at->isSameDay($date));
            
            $dayEvents = collect();
            foreach ($games as $g) {
                foreach ($g->events as $e) {
                    if ($e->end_time->isSameDay($date)) {
                        $e->game_name = $g->name;
                        $dayEvents->push($e);
                    }
                }
            }

            $dayMaint = collect();
            foreach ($games as $g) {
                foreach ($g->maintenances as $m) {
                    if ($m->start_at->isSameDay($date)) {
                        $m->game_name = $g->name;
                        $dayMaint->push($m);
                    }
                }
            }

            $calendar[] = (object) [
                'date' => $date,
                'isToday' => $i === 0,
                'tasks' => $dayTasks,
                'events' => $dayEvents,
                'maintenance' => $dayMaint,
                'hasContent' => $dayTasks->isNotEmpty() || $dayEvents->isNotEmpty() || $dayMaint->isNotEmpty()
            ];
        }

        // 8. MISSED TASKS (Keep your simpler logic or use explicit check)
        // Since we ran checkReset(), next_due_at should be correct.
        // We can simply check if next_due_at is in the past.
        $missedTasks = $todoTasks->filter(function($t) {
            return $t->isMissed();
        });

        // Return the NEW view path (dashboard.index)
        return view('dashboard.index', [
            'sortedGames' => $sortedGames,
            'alerts' => $alerts,
            'calendar' => $calendar,
            'missedTasks' => $missedTasks
        ]);
    }
}