<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->index('criteria');
        });

        Schema::table('badge_user', function (Blueprint $table) {
            $table->index(['user_id', 'badge_id']);
            $table->index('earned_at');
        });
    }

    public function down()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropIndex(['criteria']);
        });

        Schema::table('badge_user', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'badge_id']);
            $table->dropIndex(['earned_at']);
        });
    }
};