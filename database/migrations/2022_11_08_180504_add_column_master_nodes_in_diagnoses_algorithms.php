<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMasterNodesInDiagnosesAlgorithms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('case_definitions', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });

        Schema::table('dynamic_algorithm', function (Blueprint $table) {
            $table->bigInteger('master_node_id')->after('parent_id');
            $table->string('state_id')->after('index')->nullable();
            $table->string('cadre_id')->after('state_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('case_definitions', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });

        Schema::table('dynamic_algorithm', function (Blueprint $table) {
            $table->dropColumn('master_node_id');
            $table->dropColumn('state_id');
            $table->dropColumn('cadre_id');
        });
    }
}
