<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('proposal_name')->nullable()->after('description');
            $table->string('proponent')->nullable()->after('proposal_name');
            $table->string('account_information')->nullable()->after('proponent');
            $table->string('files')->nullable()->after('account_information');
            $table->string('delivery_date')->nullable()->after('files');
            $table->string('delivery_time')->nullable()->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('proposal_name');
            $table->dropColumn('proponent');
            $table->dropColumn('account_information');
            $table->dropColumn('files');
            $table->dropColumn('delivery_date');
            $table->dropColumn('delivery_time');
        });
    }
}
