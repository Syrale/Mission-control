<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameEvent; // Import this!
use Illuminate\Http\Request;

class GameEventController extends Controller
{
    public function store(Request $request, Game $game)
    {
        // 1. Check if we are in "Duration Mode" (Time Remaining)
        if ($request->has('duration_mode') && $request->duration_mode == '1') {
            $days = (int) ($request->duration_days ?? 0);
            $hours = (int) ($request->duration_hours ?? 0);
            
            // Set Start to NOW, and End to NOW + Duration
            $start = now();
            $end = now()->addDays($days)->addHours($hours);
            
            // Merge these calculated dates into the request so validation passes
            $request->merge([
                'start_time' => $start,
                'end_time' => $end,
            ]);
        }

        // 2. Normal Validation (Same as before)
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|in:event,banner,patch',
        ]);

        // 3. Create
        $game->events()->create($request->all());

        return back()->with('success', 'Timer added successfully!');
    }
        
    public function destroy(GameEvent $event)
    {
        $event->delete();
        return back();
    }
}
