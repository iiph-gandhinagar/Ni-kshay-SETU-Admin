<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResourceMaterialInChatKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_keywords', function (Blueprint $table) {
            $table->string('modules')->after('hit')->nullable();
            $table->string('sub_modules')->after('modules')->nullable();
            $table->string('resource_material')->after('sub_modules')->nullable();
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
            $table->dropColumn('modules');
            $table->dropColumn('sub_modules');
            $table->dropColumn('resource_material');
        });
    }
}
