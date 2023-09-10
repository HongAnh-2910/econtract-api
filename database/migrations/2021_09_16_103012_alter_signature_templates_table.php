<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSignatureTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signature_templates', function (Blueprint $table) {
            $table->mediumText('signature')->nullable()->change();
            $table->integer('type')->nullable();
            $table->string('path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signature_templates', function (Blueprint $table) {
            $table->mediumText('signature');
            $table->dropColumn('type');
            $table->dropColumn('path');
        });
    }
}
