<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPlateformInSubscriberActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriber_activities', function (Blueprint $table) {
            $table->enum('plateform', ['web', 'app','mobile-app'])->after('ip_address');
            $table->json('payload')->after('plateform'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriber_activities', function (Blueprint $table) {
            $table->dropColumn('plateform');
            $table->dropColumn('payload');
        });
    }
}
