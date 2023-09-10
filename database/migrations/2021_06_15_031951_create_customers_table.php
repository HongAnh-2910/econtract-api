<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->nullable();
            $table->string('name')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('name_company')->nullable();
            $table->string('address')->nullable();
            $table->string('account_number')->nullable();
            $table->string('name_bank')->nullable();
            $table->string('payments')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('customer');
    }
}
