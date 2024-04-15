<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToFarmsTable extends Migration
{
    public function up()
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->softDeletes();  // This adds the deleted_at column
        });
    }

    public function down()
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
