<?php

namespace App\Models;

use Carbon\Carbon; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 
        'name', 
        'type', 
        'is_completed', 
        'reset_hour', 
        'next_due_at', 
        'last_reset_date',
        'repeat_days' // Ensure this is here if you use loops
    ];

    protected $casts = [
        'next_due_at' => 'datetime',
        'last_reset_date' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
 * Check if the task is missed based on the RAW database value.
 */
    public function isMissed()
    {
        // 1. If it's completed, it's not missed.
        if ($this->is_completed) {
            return false;
        }

        // 2. Get the ACTUAL date stored in the DB (bypassing the Accessor)
        $rawDueAt = $this->getRawOriginal('next_due_at');

        // 3. If no date is stored, it can't be missed
        if (! $rawDueAt) {
            return false;
        }

        // 4. Check if that stored date is in the past
        return Carbon::parse($rawDueAt)->isPast();
    }

    /**
     * Check if this task needs to be reset based on time.
     */
        /**
     * Check if this task needs to be reset based on the Game's Timezone.
     */
        /**
     * Check if this task needs to be reset based on the Game's Timezone.
     */
        /**
     * Check if this task needs to be reset based on the Game's Timezone.
     */
    public function checkReset()
    {
        // If already unchecked, skip it
        if (! $this->is_completed) {
            return;
        }

        // 1. GET SETTINGS
        // Use Game timezone, default to UTC
        $timezone = $this->game->timezone ?? 'UTC';
        // Use Task reset hour, fallback to Game reset hour, fallback to 0
        $resetHour = $this->reset_hour ?? $this->game->reset_hour ?? 0;

        // 2. GET 'NOW'
        $now = Carbon::now($timezone);

        // 3. DAILY RESET LOGIC
        if ($this->type === 'daily') {
            // Calculate "Today's potential reset time"
            $todaysReset = $now->copy()->startOfDay()->addHours($resetHour);

            // DETERMINE THE *ACTUAL* LAST RESET TIME
            // If currently 2:00 AM and reset is 4:00 AM, we haven't hit today's reset yet.
            // So the "last reset" was Yesterday at 4:00 AM.
            if ($now->lt($todaysReset)) {
                $lastResetTime = $todaysReset->copy()->subDay();
            } else {
                // We are past 4:00 AM, so the last reset was Today at 4:00 AM.
                $lastResetTime = $todaysReset;
            }

            // COMPARE: If task was done BEFORE the last reset, clear it.
            if ($this->updated_at->lt($lastResetTime)) {
                $this->update(['is_completed' => false]);
            }
        }

        // 4. WEEKLY RESET LOGIC (Resets on Monday at reset_hour)
        if ($this->type === 'weekly') {
            $thisWeeksReset = $now->copy()->startOfWeek()->addHours($resetHour);

            // Same logic: If we are currently Monday 2:00 AM, we haven't hit the weekly reset yet.
            if ($now->lt($thisWeeksReset)) {
                $lastResetTime = $thisWeeksReset->copy()->subWeek();
            } else {
                $lastResetTime = $thisWeeksReset;
            }

            if ($this->updated_at->lt($lastResetTime)) {
                $this->update(['is_completed' => false]);
            }
        }

        // 5. LOOP RESET (Unchanged, relies on explicit dates)
        if ($this->type === 'loop' && $this->repeat_days) {
            $lastReset = Carbon::parse($this->last_reset_date, $timezone);
            
            $nextReset = $lastReset->copy()
                            ->addDays($this->repeat_days)
                            ->setHour($resetHour)
                            ->setMinute(0)
                            ->setSecond(0);

            if ($now->gte($nextReset)) {
                $this->update([
                    'is_completed' => false,
                    'last_reset_date' => $now, 
                ]);
            }
        }
    }

        /**
     * Calculate the NEXT deadline/reset time for this task.
     * This is used for the dashboard countdowns.
     */
    public function getNextDueAtAttribute()
    {
        // 1. Setup Config
        $timezone = $this->game->timezone ?? 'UTC';
        $resetHour = $this->reset_hour ?? $this->game->reset_hour ?? 0;
        $now = Carbon::now($timezone);

        // 2. Daily Logic
        if ($this->type === 'daily') {
            $todaysReset = $now->copy()->startOfDay()->addHours($resetHour);

            // If we haven't hit today's reset yet (e.g. it's 2AM, reset is 4AM), 
            // then the deadline is Today 4AM.
            // If we passed 4AM, the deadline is Tomorrow 4AM.
            return $now->lt($todaysReset) 
                ? $todaysReset 
                : $todaysReset->addDay();
        }

        // 3. Weekly Logic (Resets Monday)
        if ($this->type === 'weekly') {
            $thisWeeksReset = $now->copy()->startOfWeek()->addHours($resetHour);

            // If we haven't hit this week's reset yet (Monday morning), that's the deadline.
            // Otherwise, it's next week.
            return $now->lt($thisWeeksReset)
                ? $thisWeeksReset
                : $thisWeeksReset->addWeek();
        }

        // 4. Loop Logic
        if ($this->type === 'loop' && $this->repeat_days) {
            $lastReset = Carbon::parse($this->last_reset_date, $timezone);
            
            return $lastReset->copy()
                ->addDays($this->repeat_days)
                ->setHour($resetHour)
                ->setMinute(0)
                ->setSecond(0);
        }

        // Default fallback (shouldn't happen for valid types)
        return null;
    }

    /**
     * Recalculate and SAVE the persistent next_due_at column.
     * Call this when Game settings change.
     */
    public function recalculateDueAt()
    {
        // 1. Get Game Settings
        $timezone = $this->game->timezone ?? 'UTC';
        $resetHour = $this->game->reset_hour ?? 0;
        
        $now = Carbon::now($timezone);
        $target = null;

        // 2. Calculate based on Type
        if ($this->type === 'daily') {
            $target = $now->copy()->startOfDay()->addHours($resetHour);
            if ($now->gte($target)) {
                $target->addDay();
            }
        }
        elseif ($this->type === 'weekly') {
            $target = $now->copy()->startOfWeek()->addHours($resetHour);
            if ($now->gte($target)) {
                $target->addWeek();
            }
        }
        elseif ($this->type === 'loop' && $this->repeat_days && $this->last_reset_date) {
             // For loops, base it off the last reset
             $target = $this->last_reset_date->copy()->addDays($this->repeat_days);
        }
        
        // 3. Save changes if we found a target
        if ($target) {
            // Important: Convert back to UTC before saving if your DB stores in UTC
            // But typically Laravel casts handle this.
            $this->next_due_at = $target;
            $this->save();
        }
    }
}