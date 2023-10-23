<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PatientScoreDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'patient_assessment_id',
        'nikshay_id',
        'patient_name',
        'PULSE_RATE_SCORE',
        'TEMPERATURE_SCORE',
        'BLOOD_PRESSURE_SCORE',
        'RESPIRATORY_RATE_SCORE',
        'OXYGEN_SATURATION_SCORE',
        'TEXT_BMI_SCORE',
        'TEXT_MUAC_SCORE',
        'PEDAL_OEDEMA_SCORE',
        'GENERAL_CONDITION_SCORE',
        'TEXT_ICTERUS_SCORE',
        'TEXT_HEMOGLOBIN_SCORE',
        'COUNT_WBC_SCORE',
        'TEXT_RBS_SCORE',
        'TEXT_HIV_SCORE',
        'TEXT_XRAY_SCORE',
        'TEXT_HEMOPTYSIS_SCORE',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];

}
