<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnHrmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('human_resource_managers', function (Blueprint $table) {
            $table->renameColumn('account_name', 'name_account');
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
            $table->renameColumn('name_account', 'account_name');
        });
    }
}
