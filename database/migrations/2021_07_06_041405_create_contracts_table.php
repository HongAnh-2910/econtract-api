<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('sample_contract_id')->nullable();
            $table->foreign('sample_contract_id')->references('id')->on('sample_contract')->onDelete('cascade');
            $table->integer('species_contract')->nullable();
            $table->timestamp('created_contract')->nullable();
            $table->string('code_fax')->nullable();
            $table->string('name_customer')->nullable();
            $table->string('email')->nullable();
            $table->string('name_cty')->nullable();
            $table->string('address')->nullable();
            $table->string('name_account')->nullable();
            $table->unsignedBigInteger('banking_id')->nullable();
            $table->foreign('banking_id')->references('id')->on('banking')->onDelete('cascade');
            $table->integer('payments')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('contracts');
    }
}
