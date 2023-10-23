<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->integer('country_id')->default(0)->after('cadre_id');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->integer('country_id')->default(0)->after('cadre_id');
        });

        Schema::table('state', function (Blueprint $table) {
            $table->integer('country_id')->default(1)->after('id');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->integer('country_id')->default(1)->after('id');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->integer('country_id')->default(1)->after('id');
        });

        Schema::table('health_facilities', function (Blueprint $table) {
            $table->integer('country_id')->default(1)->after('id');
        });

        Schema::table('resource_materials', function (Blueprint $table) {
            $table->integer('country_id')->default(0)->after('type_of_materials');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->integer('country_id')->default(0)->after('user_id');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });

        Schema::table('resource_materials', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
    }
}
