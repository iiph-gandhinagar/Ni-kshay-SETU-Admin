<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCountsInAutomaticNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_notifications', function (Blueprint $table) {
            $table->integer('successful_count')->nullable()->after('created_by');
            $table->integer('failed_count')->nullable()->after('successful_count');
            $table->string('status')->nullable()->after('failed_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automatic_notifications', function (Blueprint $table) {
            //
        });
    }
}
