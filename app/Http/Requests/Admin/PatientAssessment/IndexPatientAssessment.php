<?php

namespace App\Http\Requests\Admin\PatientAssessment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexPatientAssessment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.patient-assessment.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:id,nikshay_id,patient_name,age,gender,patient_selected_data,PULSE_RATE,TEMPERATURE,BLOOD_PRESSURE,RESPIRATORY_RATE,OXYGEN_SATURATION,TEXT_BMI,TEXT_MUAC,PEDAL_OEDEMA,GENERAL_CONDITION,TEXT_ICTERUS,TEXT_HEMOGLOBIN,COUNT_WBC,TEXT_RBS,TEXT_HIV,TEXT_XRAY,TEXT_HEMOPTYSIS|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
