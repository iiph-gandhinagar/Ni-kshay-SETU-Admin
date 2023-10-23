<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assessment_id');
            $table->integer('user_id');
            $table->integer('total_marks');
            $table->integer('obtained_marks');
            $table->integer('attempted');
            $table->integer('right_answers');
            $table->integer('wrong_answers');
            $table->integer('skipped');
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
        Schema::dropIfExists('user_assessments');
    }
}
