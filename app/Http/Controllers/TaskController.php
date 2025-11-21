<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Game $game)
    {
        // 1. Validate the new inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily,weekly,custom,loop', // Make sure 'loop' is here
            'repeat_days' => 'nullable|integer|min:1', // Allow the days input
        ]);

        // 2. Create the task
        $game->tasks()->create([
            'name' => $request->name,
            'type' => $request->type,
            'reset_hour' => $game->reset_hour,
            
            // Save the new fields
            'repeat_days' => $request->repeat_days,
            // If it's a loop, set the start date to NOW. Otherwise null.
            'last_reset_date' => ($request->type === 'loop') ? now() : null,
        ]);

        return back();
    }

    public function toggle(Task $task)
    {
        // Flip the boolean (true -> false, false -> true)
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
}