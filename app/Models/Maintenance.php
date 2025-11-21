<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'title', 'start_at', 'end_at'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
