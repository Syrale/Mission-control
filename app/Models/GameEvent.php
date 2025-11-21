<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class GameEvent extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'name', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
