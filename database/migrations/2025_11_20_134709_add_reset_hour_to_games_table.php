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
        Schema::table('games', function (Blueprint $table) {
            // We check if the column exists first to avoid errors
            if (!Schema::hasColumn('games', 'timezone')) {
                $table->string('timezone')->default('UTC');
            }
            if (!Schema::hasColumn('games', 'reset_hour')) {
                $table->integer('reset_hour')->default(4); // Default 4 AM
            }
            if (!Schema::hasColumn('games', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'reset_hour', 'notes']);
        });
    }
};
