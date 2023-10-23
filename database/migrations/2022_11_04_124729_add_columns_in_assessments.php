<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInAssessments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('assessment_type')->after('assessment_title')->nullable()->default('real_time');
            $table->string('from_date')->after('assessment_type')->nullable();
            $table->string('to_date')->after('from_date')->nullable();
            $table->boolean('initial_invitation')->after('to_date')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('assessment_type');
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');
            $table->dropColumn('initial_invitation');
        });
    }
}
