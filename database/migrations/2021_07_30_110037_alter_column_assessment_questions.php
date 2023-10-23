<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnAssessmentQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn('question');
            $table->dropColumn('option1');
            $table->dropColumn('option2');
            $table->dropColumn('option3');
            $table->dropColumn('option4');
            $table->renameColumn('question_value_json', 'question');
            $table->renameColumn('option1_value_json', 'option1');
            $table->renameColumn('option2_value_json', 'option2');
            $table->renameColumn('option3_value_json', 'option3');
            $table->renameColumn('option4_value_json', 'option4');

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
            $table->renameColumn('question', 'question_value_json');
            $table->renameColumn('option1', 'option1_value_json');
            $table->renameColumn('option2', 'option2_value_json');
            $table->renameColumn('option3', 'option3_value_json');
            $table->renameColumn('option4', 'option4_value_json');
        });
    }
}
