<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnChatKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_keywords', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        Schema::table('chat_keywords', function (Blueprint $table) {
            $table->renameColumn('title_json', 'title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_keywords', function (Blueprint $table) {
            //
        });
    }
}
