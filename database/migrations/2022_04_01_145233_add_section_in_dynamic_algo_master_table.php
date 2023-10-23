<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionInDynamicAlgoMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dynamic_algo_master', function (Blueprint $table) {
            $table->string('section')->after('name');
            $table->softDeletes()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dynamic_algo_master', function (Blueprint $table) {
            $table->dropColumn('section');
            $table->dropColumn('deleted_at');
        });
    }
}
