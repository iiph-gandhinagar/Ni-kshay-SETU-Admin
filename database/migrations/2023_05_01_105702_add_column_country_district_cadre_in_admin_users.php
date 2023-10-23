<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCountryDistrictCadreInAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->string('role_type')->after('language')->default('state_type');
            $table->string('country')->after('role_type')->nullable();
            $table->text('district')->after('state');
            $table->string('cadre_type')->after('district');
            $table->text('cadre')->after('cadre_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('role_type');
            $table->dropColumn('country');
            $table->dropColumn('district');
            $table->dropColumn('cadre_type');
            $table->dropColumn('cadre');
        });
    }
}
