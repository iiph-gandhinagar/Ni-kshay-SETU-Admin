<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAssessmentQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->json('question_value_json')->after('order_index');
            $table->json('option1_value_json')->after('question_value_json');
            $table->json('option2_value_json')->after('option1_value_json');
            $table->json('option3_value_json')->after('option2_value_json');
            $table->json('option4_value_json')->after('option3_value_json');
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
            $table->dropColumn('question_value_json');
            $table->dropColumn('option1_value_json');
            $table->dropColumn('option2_value_json');
            $table->dropColumn('option3_value_json');
            $table->dropColumn('option4_value_json');
        });
    }
}
