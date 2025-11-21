<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // RELATIONSHIPS
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

        // Add this function!
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
