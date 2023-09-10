<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTableHrm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('human_resource_managers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_hrm')->nullable();
            $table->foreign('user_hrm')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('human_resource_managers', function (Blueprint $table) {
            $table->dropColumn('user_hrm');
        });
    }
}
