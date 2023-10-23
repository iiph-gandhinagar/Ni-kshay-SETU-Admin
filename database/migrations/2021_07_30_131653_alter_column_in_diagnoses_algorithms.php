<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnInDiagnosesAlgorithms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses_algorithms', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->renameColumn('title_value_json', 'title');
            $table->renameColumn('description_value_json', 'description');
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
            $table->renameColumn('title', 'title_value_json');
            $table->renameColumn('description', 'description_value_json');
        });
    }
}
