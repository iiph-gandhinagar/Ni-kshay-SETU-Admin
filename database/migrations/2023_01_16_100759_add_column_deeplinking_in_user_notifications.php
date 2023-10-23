<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDeeplinkingInUserNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->boolean('is_deeplinking')->after('cadre_id')->default(0);
            $table->string('automatic_notification_type')->after('is_deeplinking')->nullable();
            $table->string('type_title')->after('automatic_notification_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn('is_deeplinking');
            $table->dropColumn('automatic_notification_type');
            $table->dropColumn('type_title');
        });
    }
}
