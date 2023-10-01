<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeTableApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('applications' , 'type'))
        {
            Schema::table('applications', function (Blueprint $table) {
                $table->integer('type')
                      ->default(\App\Enums\ApplicationStatus::CREATE_APPLICATION)
                      ->comment('1:đơn từ , 2:đơn đề nghị');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('applications' , 'type' ))
        {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
}
