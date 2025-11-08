<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuccessfulPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('successful_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('payment_reference')->unique();
            $table->integer('ranc_amount');
            $table->string('package_type');
            $table->json('payment_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('successful_payments');
    }
}
