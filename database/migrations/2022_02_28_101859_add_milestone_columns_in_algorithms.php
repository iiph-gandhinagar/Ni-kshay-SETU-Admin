<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMilestoneColumnsInAlgorithms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
        });
        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
        });
        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
        });
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->json('header')->nullable()->after('redirect_node_id');
            $table->json('sub_header')->nullable()->after('header');
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
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
        Schema::table('cgc_interventions_algorithms', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
        Schema::table('differential_care_algorithms', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
        });
    }
}
