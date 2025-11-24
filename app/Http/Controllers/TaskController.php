<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 1. Create a new Task
    public function store(Request $request, Game $game)
    {
        // 1. Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily,weekly,monthly,custom,loop', // Added monthly
            'repeat_days' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
        ]);

        // 2. Calculate Due Date
        $repeatDays = (int) ($request->repeat_days ?? 1);
        
        // Determine the base date (Now, or User specified)
        $baseDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now();

        $nextDue = match($request->type) {
            'daily'   => $game->next_reset,
            'weekly'  => $game->next_reset->addDays(7),
            'monthly' => $baseDate->copy()->addMonth(), // Smart Month Math
            'loop'    => $baseDate->copy()->addDays($repeatDays), // Fixed Day Math
            default   => $game->next_reset,
        };

        // 3. Create Task
        $game->tasks()->create([
            'name' => $request->name,
            'priority' => 'medium',
            'type' => $request->type,
            'reset_hour' => $game->reset_hour,
            'repeat_days' => $repeatDays,
            // For Monthly/Loop, we store the start date so we can calculate future resets
            'last_reset_date' => ($request->type === 'loop' || $request->type === 'monthly') ? $baseDate : null,
            'next_due_at' => $nextDue,
            'is_completed' => false,
        ]);

        return back()->with('success', 'Task created successfully.');
    }
    
    // 2. Mark task as Complete (Used by Dashboard Checkmark)
    public function complete(Task $task)
    {
        if ($task->game->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update(['is_completed' => true]);

        return back()->with('success', 'Task completed!');
    }

    // 3. Toggle status (Keep this if you use it elsewhere)
    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);
        return back();
    }

    // 4. Mark ALL Missed Tasks as Complete (Used by Red Alert Box)
    public function completeMissed()
    {
        $user = auth()->user();
        
        // Find all tasks belonging to this user's games
        $missedTasks = Task::whereHas('game', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('is_completed', false)
        ->where('next_due_at', '<', now()) // Due date is in the past
        ->get();

        foreach ($missedTasks as $task) {
            $task->update(['is_completed' => true]);
        }

        return back()->with('success', 'All missed tasks marked as done.');
    }

    // 5. Delete Task
    public function destroy(Task $task)
    {
        if ($task->game->user_id !== auth()->id()) {
            abort(403);
        }
        
        $task->delete();
        return back();
    }
}