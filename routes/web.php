<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GameEventController;
use App\Http\Controllers\DashboardController;
use App\Models\Game; // <--- Don't forget to import this at the top!


// Public Page (Welcome)
Route::get('/', function () {
    return view('welcome');
});

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // 1. Route for the Checkmark Button on Dashboard
    Route::post('/tasks/{task}/complete', [\App\Http\Controllers\TaskController::class, 'complete'])
        ->name('tasks.complete');

    // 2. Route for the "Mark All Late" Button
    Route::post('/tasks/complete-missed', [\App\Http\Controllers\TaskController::class, 'completeMissed'])
        ->name('tasks.complete_missed');
});

require __DIR__.'/auth.php';