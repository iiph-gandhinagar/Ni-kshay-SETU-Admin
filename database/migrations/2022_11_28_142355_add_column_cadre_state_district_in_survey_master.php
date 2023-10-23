<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCadreStateDistrictInSurveyMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_master', function (Blueprint $table) {
            $table->string('country_id')->after('title')->default(0);
            $table->string('cadre_id')->after('country_id');
            $table->string('state_id')->after('cadre_id');
            $table->text('district_id')->after('state_id')->nullable();
            $table->string('cadre_type')->after('district_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_master', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('cadre_id');
            $table->dropColumn('state_id');
            $table->dropColumn('district_id');
            $table->dropColumn('cadre_type');
        });
    }
}
