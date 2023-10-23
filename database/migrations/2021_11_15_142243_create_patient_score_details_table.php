<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientScoreDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_score_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_assessment_id');
            $table->string('nikshay_id');
            $table->string('patient_name');
            $table->float('PULSE_RATE_SCORE');
            $table->float('TEMPERATURE_SCORE');
            $table->string('BLOOD_PRESSURE_SCORE');
            $table->float('RESPIRATORY_RATE_SCORE');
            $table->float('OXYGEN_SATURATION_SCORE');
            $table->float('TEXT_BMI_SCORE');
            $table->float('TEXT_MUAC_SCORE');
            $table->string('PEDAL_OEDEMA_SCORE');
            $table->string('GENERAL_CONDITION_SCORE');
            $table->string('TEXT_ICTERUS_SCORE');
            $table->float('TEXT_HEMOGLOBIN_SCORE');
            $table->float('COUNT_WBC_SCORE');
            $table->float('TEXT_RBS_SCORE');
            $table->string('TEXT_HIV_SCORE');
            $table->string('TEXT_XRAY_SCORE');
            $table->string('TEXT_HEMOPTYSIS_SCORE');
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
        Schema::dropIfExists('patient_score_details');
    }
}
