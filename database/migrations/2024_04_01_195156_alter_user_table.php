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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('reputation')->default(0); //repa
            $table->unsignedInteger('land_id')->default(1); // location
            $table->unsignedInteger('posx')->default(50); //pos x
            $table->unsignedInteger('posy')->default(50); //pos y
            $table->timestamp('active_at')->nullable(); //last active
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reputation', 'land_id', 'posx', 'posy', 'active_at']);
        });
    }
};
