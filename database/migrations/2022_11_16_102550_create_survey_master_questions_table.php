<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyMasterQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_master_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('survey_master_id');
            $table->json('question');
            $table->string('type');
            $table->json('option1')->nullable();
            $table->json('option2')->nullable();
            $table->json('option3')->nullable();
            $table->json('option4')->nullable();
            $table->integer('order_index');
            $table->boolean('active');
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
        Schema::dropIfExists('survey_master_questions');
    }
}
