<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GameEventController;
use App\Models\Game; // <--- Don't forget to import this at the top!


// Public Page (Welcome)
Route::get('/', function () {
    return view('welcome');
});

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Controller
    Route::get('/dashboard', function () {
        // 1. Load Data (SCOPED TO USER NOW)
        $games = Game::where('user_id', auth()->id()) // <--- THIS IS THE FIX
                     ->with(['tasks', 'events', 'maintenances'])
                     ->get();

        // 2. Run Reset Logic
        $games->each(function ($game) {
            $game->tasks->each->checkReset();
        });

        // 3. Calculate Variables
        $now = now();

        // Get all Maintenances happening right now
        $activeMaintenances = $games->flatMap->maintenances->filter(function ($m) use ($now) {
            return $now->between($m->start_at, $m->end_at);
        });

        // Get ALL incomplete tasks (after reset logic)
        $allPendingTasks = $games->flatMap->tasks->filter(function ($t) {
            return !$t->is_completed;
        });

        // --- NEW: URGENT NOTICES (Time Remaining Logic) ---
        
        // A. CRITICAL: Expiring in less than 3 hours! (Red Box)
        $overdueTasks = $allPendingTasks->filter(function ($t) use ($now) {
            return $t->next_due_at && $t->next_due_at->diffInHours($now) < 3;
        })->sortBy('next_due_at');

        // B. IMMINENT: Expiring between 3 and 24 hours (Amber Box)
        $imminentTasks = $allPendingTasks->filter(function ($t) use ($now) {
            $hoursLeft = $t->next_due_at ? $t->next_due_at->diffInHours($now) : 999;
            return $hoursLeft >= 3 && $hoursLeft < 24;
        })->sortBy('next_due_at');


        return view('dashboard', [
            'games' => $games,
            'activeMaintenances' => $activeMaintenances,
            'todoTasks' => $allPendingTasks, 
            'overdueTasks' => $overdueTasks, 
            'imminentTasks' => $imminentTasks, 
        ]);
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Game Routes (Handles Index, Create, Store, Show, Edit, Update, Destroy)
    Route::resource('games', GameController::class);

    // Create a task (Nested under games)
    Route::post('/games/{game}/tasks', [TaskController::class, 'store'])->name('games.tasks.store');

    // Toggle a task
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

    // Delete a task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Event Routes (New!)
    Route::post('/games/{game}/events', [GameEventController::class, 'store'])->name('games.events.store');
    Route::delete('/events/{event}', [GameEventController::class, 'destroy'])->name('events.destroy');

        // Maintenance Routes
    Route::post('/games/{game}/maintenances', [App\Http\Controllers\MaintenanceController::class, 'store'])->name('games.maintenances.store');
    Route::delete('/maintenances/{maintenance}', [App\Http\Controllers\MaintenanceController::class, 'destroy'])->name('maintenances.destroy');
});

require __DIR__.'/auth.php';