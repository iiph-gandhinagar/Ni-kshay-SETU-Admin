<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInChatQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_questions', function (Blueprint $table) {
            $table->json('question_json')->nullable()->after('question');
            $table->json('answer_json')->nullable()->after('answer');
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
            $table->dropColumn('question_json');
            $table->dropColumn('answer_json');
        });
    }
}
