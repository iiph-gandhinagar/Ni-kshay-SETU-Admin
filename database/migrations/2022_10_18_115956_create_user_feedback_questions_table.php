<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeedbackQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_feedback_questions', function (Blueprint $table) {
            $table->id();
            $table->json("feedback_question");
            $table->json("feedback_description")->nullable();
            $table->string("feedback_value");
            $table->string("feedback_time")->nullable();
            $table->string("feedback_type");
            $table->integer("feedback_days")->nullable();
            $table->boolean("is_active")->default(0);
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
        Schema::dropIfExists('user_feedback_questions');
    }
}
