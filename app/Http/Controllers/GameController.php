<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function index()
    {
        // The dashboard logic is handled in routes/web.php
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('games.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. VALIDATION
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'developer' => 'nullable|string|max:255',
            
            // FIXED: Changed from strict Rule::in(...) to just 'string'
            // This allows manual offsets like "-07:00" to pass validation
            'timezone' => 'required|string',
            
            'reset_hour' => 'required|integer|min:0|max:23', 
            'notes' => 'nullable|string',
        ]);

        // 2. CREATE THE GAME
        $request->user()->games()->create($validated);

        // 3. REDIRECT
        return redirect()->route('dashboard')->with('status', 'Game added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        // Check reset logic before showing
        foreach ($game->tasks as $task) {
            $task->checkReset();
        }

        return view('games.show', compact('game'));
    }

    /**
     * Show the Edit Form
     */
    public function edit(Game $game)
    {
        return view('games.edit', compact('game'));
    }

    /**
     * Process the Update
     */
    public function update(Request $request, Game $game)
    {
        // Ensure user owns the game
        if ($request->user()->id !== $game->user_id) {
            abort(403);
        }

        // 1. VALIDATE FIRST
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'developer' => 'nullable|string|max:255',
            
            // FIXED: Changed from strict Rule::in(...) to just 'string'
            // This allows manual offsets like "-07:00" to pass validation
            'timezone' => 'required|string',
            
            'reset_hour' => 'required|integer|min:0|max:23',
            'notes' => 'nullable|string',
        ]);

        // 2. UPDATE AFTER VALIDATION PASSES
        $game->update($validated);
        
        // Redirect back to dashboard or show page
        return redirect()->route('games.show', $game)->with('success', 'Game updated successfully!');
    }

    public function destroy(Game $game)
    {
        // Security check
        if (Auth::id() !== $game->user_id) {
            abort(403);
        }

        $game->delete();

        return redirect()->route('dashboard')
            ->with('status', 'Game deleted successfully!');
    }
}