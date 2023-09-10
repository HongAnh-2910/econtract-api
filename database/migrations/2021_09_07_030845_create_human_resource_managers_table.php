<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHumanResourceManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('human_resource_managers', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->string('phone_number');
            $table->string('date_start');
            $table->string('form');
            $table->string('date_of_birth');
            $table->string('passport');
            $table->string('date_range');
            $table->string('place_range');
            $table->string('permanent_address');
            $table->string('current_address');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('name_bank');
            $table->string('motorcycle_license_plate');
            $table->string('file');
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
        Schema::dropIfExists('human_resource_managers_tables');
    }
}
