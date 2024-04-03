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
//        Schema::disableForeignKeyConstraints();

        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nft')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('size');
            $table->timestamps();
        });

//        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};
