<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPlateformInUserAppVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_app_version', function (Blueprint $table) {
            $table->string('current_plateform')->after('app_version');
            $table->boolean('has_ios')->after('current_plateform')->default(0);
            $table->boolean('has_android')->after('has_ios')->default(0);
            $table->boolean('has_web')->after('has_android')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_app_version', function (Blueprint $table) {
            $table->dropColumn('current_plateform');
            $table->dropColumn('has_ios');
            $table->dropColumn('has_android');
            $table->dropColumn('has_web');
        });
    }
}
