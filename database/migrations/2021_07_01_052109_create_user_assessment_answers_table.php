<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAssessmentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assessment_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assessment_id');
            $table->integer('user_id');
            $table->integer('question_id');
            $table->string('answer');
            $table->boolean('is_correct');
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
        Schema::dropIfExists('user_assessment_answers');
    }
}
