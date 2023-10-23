<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSendInitialNotificationInSurveyMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_master', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('active')->default(0);
        });

        Schema::table('case_definitions', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('dynamic_algorithm', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
        });

        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->boolean('send_initial_notification')->after('activated')->default(0);
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
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('dynamic_algorithm', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->dropColumn('send_initial_notification');
        });
    }
}
