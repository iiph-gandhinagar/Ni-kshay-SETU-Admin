<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_no');
            $table->integer('user_id');
            $table->integer('otp');
            $table->boolean('is_verified')->default(0);
            $table->string('message_body');
            $table->boolean('is_delivered')->default(0);
            $table->string('via')->nullable();
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
        Schema::dropIfExists('otp_request');
    }
}
