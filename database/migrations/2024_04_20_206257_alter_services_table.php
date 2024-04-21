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
        Schema::table('services', function (Blueprint $table) {

            $table->string('image_init')->nullable();
            $table->string('image_ready')->nullable();
            $table->string('image_reload')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->dropColumn('image_init');
            $table->dropColumn('image_ready');
            $table->dropColumn('image_reload');

        });
    }
};
