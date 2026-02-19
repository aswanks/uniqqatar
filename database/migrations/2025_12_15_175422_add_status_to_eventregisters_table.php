<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToEventregistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eventregisters', function (Blueprint $table) {
            $table->tinyInteger('adult_status')->default(1)->after('number_adult');
            $table->tinyInteger('child_status')->default(1)->after('number_child');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eventregisters', function (Blueprint $table) {
            $table->dropColumn(['adult_status', 'child_status']);
        });
    }
}