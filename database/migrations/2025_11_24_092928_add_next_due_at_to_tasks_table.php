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
        Schema::table('tasks', function (Blueprint $table) {
            // We add next_due_at. Nullable is safer for existing rows.
            $table->timestamp('next_due_at')->nullable()->after('is_completed');
            
            // Just in case you are missing last_reset_date too (based on your code context)
            // If this column already exists, you can remove this line.
            if (!Schema::hasColumn('tasks', 'last_reset_date')) {
                $table->timestamp('last_reset_date')->nullable()->after('repeat_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['next_due_at', 'last_reset_date']);
        });
    }
};