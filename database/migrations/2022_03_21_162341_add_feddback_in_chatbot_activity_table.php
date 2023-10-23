<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeddbackInChatbotActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('t_feedback_handling');
        
        Schema::table('chatbot_activity', function (Blueprint $table) {
            $table->integer('tag_id')->default(0)->after('ip_address');
            $table->integer('question_id')->default(0)->after('tag_id');
            $table->integer('like')->default(0)->after('question_id');
            $table->integer('dislike')->default(0)->after('like');
        });

        Schema::table('t_training_tag', function (Blueprint $table) {
            $table->integer('like_count')->default(0)->after('is_fix_response');
            $table->integer('dislike_count')->default(0)->after('like_count');
        });

        Schema::table('chat_questions', function (Blueprint $table) {
            $table->integer('like_count')->default(0)->after('activated');
            $table->integer('dislike_count')->default(0)->after('like_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_activity', function (Blueprint $table) {
            $table->dropColumn('tag_id');
            $table->dropColumn('question_id');
            $table->dropColumn('like');
            $table->dropColumn('dislike');
        });

        Schema::table('t_training_tag', function (Blueprint $table) {
            $table->dropColumn('like_count');
            $table->dropColumn('dislike_count');
        });

        Schema::table('chat_questions', function (Blueprint $table) {
            $table->dropColumn('like_count');
            $table->dropColumn('dislike_count');
        });
    }
}
