<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUserAssessments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessments', function (Blueprint $table) {
            $table->string('total_time')->after('total_marks');
            $table->boolean('is_calculated')->after('skipped')->default(0);
            $table->integer('obtained_marks')->default(0)->change();
            $table->integer('attempted')->default(0)->change();
            $table->integer('right_answers')->default(0)->change();
            $table->integer('wrong_answers')->default(0)->change();
            $table->integer('skipped')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_assessments', function (Blueprint $table) {
            $table->dropColumn('total_time');
            $table->dropColumn('is_calculated');
        });
    }
}
