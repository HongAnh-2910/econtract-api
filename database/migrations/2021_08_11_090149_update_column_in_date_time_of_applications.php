<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnInDateTimeOfApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('date_time_of_applications', function (Blueprint $table) {
            $table->dateTime('information_day_2')->nullable()->change();
            $table->dateTime('information_day_4')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('date_time_of_applications', function (Blueprint $table) {
            $table->string('information_day_2')->nullable()->change();
            $table->string('information_day_4')->nullable()->change();
        });
    }
}
