<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToDiagnosesAlgorithms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->json("title_value_json")->after('index');
            $table->json("description_value_json")->after('title_value_json');
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
            $table->dropColumn('title_value_json');
            $table->dropColumn('description_value_json');
        });
    }
}
