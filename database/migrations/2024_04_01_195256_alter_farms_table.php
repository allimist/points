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

            $table->unsignedInteger('size')->default(1);
            $table->unsignedInteger('posx')->nullable();
            $table->unsignedInteger('posy')->nullable();
            $table->boolean('is_public')->default(1);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {

            $table->dropColumn('size');
            $table->dropColumn('posx');
            $table->dropColumn('posy');
            $table->dropColumn('is_public');

        });
    }
};
