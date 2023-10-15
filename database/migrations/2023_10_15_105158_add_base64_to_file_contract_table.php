<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBase64ToFileContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_contract', function (Blueprint $table) {
            $table->longText('base64')->after('file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('file_contract' ,'base64'))
        {
            Schema::table('file_contract', function (Blueprint $table) {
                $table->dropColumn('base64');
            });
        }
    }
}
