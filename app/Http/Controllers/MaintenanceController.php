<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    // UPDATE: Accept Game $game automatically from the URL
    public function store(Request $request, Game $game)
    {
        $data = $request->validate([
            // REMOVED 'game_id' validation (it is already in $game)
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ]);

        // 1. Use the Game's Timezone
        $timezone = $game->timezone ?? 'UTC';

        // 2. Parse input using Game Time -> Convert to UTC
        $startAt = Carbon::parse($data['start_at'], $timezone)->setTimezone('UTC');
        $endAt = Carbon::parse($data['end_at'], $timezone)->setTimezone('UTC');

        // 3. Create Maintenance linked to the $game
        $game->maintenances()->create([
            'title' => $data['title'],
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return redirect()->back()->with('success', 'Maintenance scheduled!');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->back()->with('success', 'Maintenance removed.');
    }
}