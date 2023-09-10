<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banking', function (Blueprint $table) {
            $table->id();
            $table->string('en_name')->nullable();
            $table->string('vn_name')->nullable();
            $table->integer('bankId')->nullable();
            $table->integer('atmBin')->nullable();
            $table->integer('cardLength')->nullable();
            $table->string('shortName')->nullable();
            $table->integer('bankCode')->nullable();
            $table->string('type')->nullable();
            $table->string('napasSupported')->nullable();
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
        Schema::dropIfExists('banking');
    }
}
