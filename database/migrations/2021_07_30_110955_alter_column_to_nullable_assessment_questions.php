<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnToNullableAssessmentQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->json('question')->nullable()->change();
            $table->json('option1')->nullable()->change();
            $table->json('option2')->nullable()->change();
            $table->json('option3')->nullable()->change();
            $table->json('option4')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            //
        });
    }
}
