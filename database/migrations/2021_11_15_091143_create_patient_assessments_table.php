<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nikshay_id');
            $table->string('patient_name');
            $table->string('age');
            $table->string('gender');
            $table->json('patient_selected_data');
            $table->float('PULSE_RATE');
            $table->float('TEMPERATURE');
            $table->string('BLOOD_PRESSURE');
            $table->float('RESPIRATORY_RATE');
            $table->float('OXYGEN_SATURATION');
            $table->float('TEXT_BMI');
            $table->float('TEXT_MUAC');
            $table->string('PEDAL_OEDEMA');
            $table->string('GENERAL_CONDITION');
            $table->string('TEXT_ICTERUS');
            $table->float('TEXT_HEMOGLOBIN');
            $table->float('COUNT_WBC');
            $table->float('TEXT_RBS');
            $table->string('TEXT_HIV');
            $table->string('TEXT_XRAY');
            $table->string('TEXT_HEMOPTYSIS');
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
        Schema::dropIfExists('patient_assessments');
    }
}
