<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameEvent; // Import this!
use Illuminate\Http\Request;

class GameEventController extends Controller
{
    public function store(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $game->events()->create($request->only('name', 'start_time', 'end_time'));

        return back()->with('status', 'Event added!');
    }
    
    public function destroy(GameEvent $event)
    {
        $event->delete();
        return back();
    }
}
