<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTotalTimeSpentInCaseDefinitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });

        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });

        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });

        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });

        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });

        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });

        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->string('time_spent')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });
    }
}
