<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedirectionColumnsToAlgoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_definitions', function (Blueprint $table) {
            $table->string('redirect_algo_type')->after('description')->nullable();
            $table->bigInteger('redirect_node_id')->after('redirect_algo_type')->default(0);
        });
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->string('redirect_algo_type')->after('description')->nullable();
            $table->bigInteger('redirect_node_id')->after('redirect_algo_type')->default(0);
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->string('redirect_algo_type')->after('description')->nullable();
            $table->bigInteger('redirect_node_id')->after('redirect_algo_type')->default(0);
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->string('redirect_algo_type')->after('description')->nullable();
            $table->bigInteger('redirect_node_id')->after('redirect_algo_type')->default(0);
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->string('redirect_algo_type')->after('description')->nullable();
            $table->bigInteger('redirect_node_id')->after('redirect_algo_type')->default(0);
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
            $table->dropColumn('redirect_algo_type');
            $table->dropColumn('redirect_node_id');
        });
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->dropColumn('redirect_algo_type');
            $table->dropColumn('redirect_node_id');
        });
        Schema::table('guidance_on_adverse_drug_reactions', function (Blueprint $table) {
            $table->dropColumn('redirect_algo_type');
            $table->dropColumn('redirect_node_id');
        });
        Schema::table('latent_tb_infections', function (Blueprint $table) {
            $table->dropColumn('redirect_algo_type');
            $table->dropColumn('redirect_node_id');
        });
        Schema::table('treatment_algorithms', function (Blueprint $table) {
            $table->dropColumn('redirect_algo_type');
            $table->dropColumn('redirect_node_id');
        });
    }
}
