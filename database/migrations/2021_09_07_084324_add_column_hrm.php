<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnHrm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('human_resource_managers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('file');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('department_id')->nullable()->after('form');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->softDeletes()->after('department_id');
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
            $table->dropForeign('user_id');
            $table->dropColumn('user_id');
            $table->dropForeign('department_id');
            $table->dropColumn('department_id');
        });
    }
}
