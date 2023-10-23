<?php

namespace App\Http\Requests\Admin\PatientAssessment;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StorePatientAssessment extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.patient-assessment.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'nikshay_id' => ['required', 'string'],
            'patient_name' => ['required', 'string'],
            'age' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'PULSE_RATE' => ['required', 'integer'],
            'TEMPERATURE' => ['required', 'integer'],
            'BLOOD_PRESSURE' => ['required', 'string'],
            'RESPIRATORY_RATE' => ['required', 'integer'],
            'OXYGEN_SATURATION' => ['required', 'integer'],
            'TEXT_BMI' => ['required', 'integer'],
            'TEXT_MUAC' => ['required', 'integer'],
            'PEDAL_OEDEMA' => ['required', 'string'],
            'GENERAL_CONDITION' => ['required', 'string'],
            'TEXT_ICTERUS' => ['required', 'string'],
            'TEXT_HEMOGLOBIN' => ['required', 'integer'],
            'COUNT_WBC' => ['required', 'integer'],
            'TEXT_RBS' => ['required', 'integer'],
            'TEXT_HIV' => ['required', 'string'],
            'TEXT_XRAY' => ['required', 'string'],
            'TEXT_HEMOPTYSIS' => ['required', 'string'],

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
            'patient_selected_data' => ['required', 'string'],

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
