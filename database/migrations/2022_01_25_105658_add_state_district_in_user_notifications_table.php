<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStateDistrictInUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->string('state_id')->nullable()->after('user_id');
            $table->string('district_id')->nullable()->after('state_id');
            $table->string('cadre_type')->nullable()->after('district_id');
            $table->string('cadre_id')->nullable()->after('cadre_type');
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
            $table->dropColumn('state_id');
            $table->dropColumn('district_id');
            $table->dropColumn('cadre_type');
            $table->dropColumn('cadre_id');
        });
    }
}
