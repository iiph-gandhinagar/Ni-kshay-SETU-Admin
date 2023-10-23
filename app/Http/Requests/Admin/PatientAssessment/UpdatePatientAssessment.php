<?php

namespace App\Http\Requests\Admin\PatientAssessment;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdatePatientAssessment extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.patient-assessment.edit', $this->patientAssessment);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'nikshay_id' => ['sometimes', 'string'],
            'patient_name' => ['sometimes', 'string'],
            'age' => ['sometimes', 'string'],
            'gender' => ['sometimes', 'string'],
            'PULSE_RATE' => ['sometimes', 'integer'],
            'TEMPERATURE' => ['sometimes', 'integer'],
            'BLOOD_PRESSURE' => ['sometimes', 'string'],
            'RESPIRATORY_RATE' => ['sometimes', 'integer'],
            'OXYGEN_SATURATION' => ['sometimes', 'integer'],
            'TEXT_BMI' => ['sometimes', 'integer'],
            'TEXT_MUAC' => ['sometimes', 'integer'],
            'PEDAL_OEDEMA' => ['sometimes', 'string'],
            'GENERAL_CONDITION' => ['sometimes', 'string'],
            'TEXT_ICTERUS' => ['sometimes', 'string'],
            'TEXT_HEMOGLOBIN' => ['sometimes', 'integer'],
            'COUNT_WBC' => ['sometimes', 'integer'],
            'TEXT_RBS' => ['sometimes', 'integer'],
            'TEXT_HIV' => ['sometimes', 'string'],
            'TEXT_XRAY' => ['sometimes', 'string'],
            'TEXT_HEMOPTYSIS' => ['sometimes', 'string'],


        ];
    }

    /**
     * Get the validation rules that apply to the requests translatable fields.
     *
     * @return array
     */
    public function translatableRules($locale): array
    {
        return [
            'patient_selected_data' => ['sometimes', 'string'],

        ];
    }

    /**
     * Modify input data
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();


        //Add your code for manipulation with request data here

        return $sanitized;
    }
}
