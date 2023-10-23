<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;

class PatientAssessment extends Model
{
    use SoftDeletes;
use HasTranslations;
    protected $fillable = [
        'nikshay_id',
        'patient_name',
        'age',
        'gender',
        'patient_selected_data',
        'PULSE_RATE',
        'TEMPERATURE',
        'BLOOD_PRESSURE',
        'RESPIRATORY_RATE',
        'OXYGEN_SATURATION',
        'TEXT_BMI',
        'TEXT_MUAC',
        'PEDAL_OEDEMA',
        'GENERAL_CONDITION',
        'TEXT_ICTERUS',
        'TEXT_HEMOGLOBIN',
        'COUNT_WBC',
        'TEXT_RBS',
        'TEXT_HIV',
        'TEXT_XRAY',
        'TEXT_HEMOPTYSIS',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'patient_selected_data',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/patient-assessments/'.$this->getKey());
    }
}
