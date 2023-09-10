<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDateTimeOfApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_time_of_applications', function (Blueprint $table) {
            $table->id();
            $table->string('information_day_1')->nullable();
            $table->string('information_day_2')->nullable();
            $table->string('information_day_3')->nullable();
            $table->string('information_day_4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('date_time_of_application');
    }
}
