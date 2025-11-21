<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            
            // This links the task to a specific game
            // 'cascade' means if you delete the Game, the Tasks get deleted too.
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            
            $table->string('name'); // e.g., "Daily Commissions"
            $table->string('type')->default('daily'); // 'daily', 'weekly', 'custom'
            $table->boolean('is_completed')->default(false);
            $table->integer('reset_hour')->nullable(); // e.g., 4 (for 4 AM)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
