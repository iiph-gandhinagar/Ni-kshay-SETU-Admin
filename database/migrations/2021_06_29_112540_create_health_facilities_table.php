<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('health_facilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state_id');
            $table->integer('district_id');
            $table->integer('block_id');
            $table->string('health_facility_code');
            $table->double('longitude');
            $table->double('latitude');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_facilities');
    }
}
