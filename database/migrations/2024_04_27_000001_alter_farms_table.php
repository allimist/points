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
        Schema::table('farms', function (Blueprint $table) {

            $table->integer('owner_id')->nullable();
            // states
            // relax - go to maim location and relax
            // attack - go to user position and attack him
            //target - user id
            $table->string('state')->nullable();
            $table->integer('target_id')->nullable();
            //$table->dateTime('')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {

            $table->dropColumn('owner_id');
            $table->dropColumn('state');
            $table->dropColumn('target_id');

        });
    }
};
