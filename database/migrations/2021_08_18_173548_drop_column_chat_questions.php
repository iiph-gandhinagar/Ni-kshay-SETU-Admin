<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnChatQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_questions', function (Blueprint $table) {
            $table->dropColumn('question');
            $table->dropColumn('answer');
        });
        Schema::table('chat_questions', function (Blueprint $table) {
            $table->renameColumn('question_json', 'question');
            $table->renameColumn('answer_json', 'answer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_questions', function (Blueprint $table) {
            //
        });
    }
}
