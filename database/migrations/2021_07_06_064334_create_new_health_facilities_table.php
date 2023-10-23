<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewHealthFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('health_facilities');

        Schema::create('health_facilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state_id');
            $table->integer('district_id');
            $table->integer('block_id');
            $table->string('health_facility_code');
            $table->boolean('DMC');
            $table->boolean('TRUNAT');
            $table->boolean('CBNAAT');
            $table->boolean('X_RAY');
            $table->boolean('ICTC');
            $table->boolean('LPA_Lab');
            $table->boolean('CONFIRMATION_CENTER');
            $table->boolean('Tobacco_Cessation_clinic');
            $table->boolean('ANC_Clinic');
            $table->boolean('Nutritional_Rehabilitation_centre');
            $table->boolean('De_addiction_centres');
            $table->boolean('ART_Centre');
            $table->boolean('District_DRTB_Centre');
            $table->boolean('NODAL_DRTB_CENTER');
            $table->boolean('IRL');
            $table->boolean('Pediatric_Care_Facility');
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
