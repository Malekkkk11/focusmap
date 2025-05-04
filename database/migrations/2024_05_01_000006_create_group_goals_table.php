<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupGoalsTable extends Migration
{
    public function up()
    {
        Schema::create('group_goals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('participants_limit')->nullable();
            $table->foreignId('creator_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('group_goal_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_goal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0);
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_goal_user');
        Schema::dropIfExists('group_goals');
    }
}