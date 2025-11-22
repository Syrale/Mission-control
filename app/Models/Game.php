<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // <--- CRITICAL: This is required for time logic

class Game extends Model
{
    use HasFactory;

    // Whitelist these fields so we can save them easily
    protected $fillable = [
        'user_id', 
        'name', 
        'developer', 
        'timezone', 
        'reset_hour', 
        'notes'
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================
    
    // A game belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A game has many Tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function events()
    {
        return $this->hasMany(GameEvent::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    // ===========================
    // ACCESSORS (Magic Attributes)
    // ===========================

    // 1. Get Current Server Time
    // Usage: $game->current_time
    public function getCurrentTimeAttribute()
    {
        try {
            $tz = $this->timezone ?? 'UTC';
            return Carbon::now($tz);
        } catch (\Exception $e) {
            // Fallback to UTC if timezone is invalid
            return Carbon::now('UTC');
        }
    }

    // 2. Get Next Reset Time
    // Usage: $game->next_reset
    public function getNextResetAttribute()
    {
        $now = $this->current_time;
        $resetHour = $this->reset_hour ?? 0; 

        // Create a Carbon instance for Today at the Reset Hour
        $resetToday = $now->copy()->setTime($resetHour, 0, 0);

        // If we have already passed the reset hour today, the next one is tomorrow
        if ($now->greaterThanOrEqualTo($resetToday)) {
            return $resetToday->addDay();
        }

        return $resetToday;
    }

    // 3. Check if currently in Maintenance
    // Usage: $game->is_maintenance
    public function getIsMaintenanceAttribute()
    {
        // Checks if there is any maintenance record where NOW is between Start and End
        // We use 'now()' here which is UTC/Server standard time, assuming your DB stores timestamps correctly
        return $this->maintenances()
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>', now())
                    ->exists();
    }
}