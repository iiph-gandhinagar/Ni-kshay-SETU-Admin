<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnPlateformInSubscribrActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribr_activities', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `subscriber_activities` CHANGE `plateform` `plateform` ENUM('web','app','mobile-app', 'iPhone-app') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribr_activities', function (Blueprint $table) {
            //
        });
    }
}
