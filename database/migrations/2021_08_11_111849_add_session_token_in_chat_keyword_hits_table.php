<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionTokenInChatKeywordHitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_keyword_hits', function (Blueprint $table) {
            $table->string('session_token')->after('subscriber_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_keyword_hits', function (Blueprint $table) {
            $table->dropColumn('session_token');
        });
    }
}
