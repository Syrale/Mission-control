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
        // 1. Validate (removed 'priority' from required)
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily,weekly,custom,loop',
            'repeat_days' => 'nullable|integer|min:1', 
        ]);

        // 2. calculate Due Date
        $nextDue = match($request->type) {
            'daily' => $game->next_reset,
            'weekly' => $game->next_reset->addDays(7),
            'loop' => now()->addDays($request->repeat_days ?? 1),
            default => $game->next_reset,
        };

        // 3. Create (With default Priority)
        $game->tasks()->create([
            'name' => $request->name,
            'priority' => $request->priority ?? 'medium', // <--- Default if missing
            'type' => $request->type,
            'reset_hour' => $game->reset_hour,
            'repeat_days' => $request->repeat_days,
            'last_reset_date' => ($request->type === 'loop') ? now() : null,
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