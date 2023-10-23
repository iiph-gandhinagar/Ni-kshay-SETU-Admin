<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivateDeactivateOptionInAlgo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('redirect_node_id');
        });
        Schema::table('chat_questions', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('category');
        });
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('activated')->default(false)->after('assessment_title');
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
            $table->dropColumn('activated');
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('chat_questions', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('activated');
        });
    }
}
