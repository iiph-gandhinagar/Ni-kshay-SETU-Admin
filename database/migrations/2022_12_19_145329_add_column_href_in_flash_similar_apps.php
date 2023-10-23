<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnHrefInFlashSimilarApps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flash_similar_apps', function (Blueprint $table) {
            $table->string("href_web")->after('href');
            $table->string("href_ios")->after('href_web');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flash_similar_apps', function (Blueprint $table) {
            $table->dropColumn('href_web');
            $table->dropColumn('href_ios');
        });
    }
}
