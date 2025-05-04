<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('icon')->nullable();
            $table->string('criteria'); // What needs to be achieved to earn this badge
            $table->timestamps();
        });

        // Pivot table for users and their earned badges
        Schema::create('badge_user', function (Blueprint $table) {
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->primary(['badge_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
    }
};