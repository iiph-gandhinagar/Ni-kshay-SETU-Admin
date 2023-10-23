<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLocationResourceMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resource_materials', function (Blueprint $table) {
            $table->string('state')->after('type_of_materials');
            $table->string('cadre')->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resource_materials', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->dropColumn('cadre');
        });
    }
}
