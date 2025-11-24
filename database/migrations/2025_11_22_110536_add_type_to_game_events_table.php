<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('game_events', function (Blueprint $table) {
            // We add 'type' and give it a default so existing events don't break
            $table->string('type')->default('event')->after('name'); 
        });
    }

    public function down()
    {
        Schema::table('game_events', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};