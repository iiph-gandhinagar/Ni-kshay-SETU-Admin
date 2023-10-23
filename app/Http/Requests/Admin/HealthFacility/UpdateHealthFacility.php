<?php

namespace App\Http\Requests\Admin\HealthFacility;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateHealthFacility extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.health-facility.edit', $this->healthFacility);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'country_id' => ['sometimes', 'array'],
            'state_id' => ['sometimes', 'array'],
            'district_id' => ['sometimes', 'array'],
            'block_id' => ['sometimes', 'array'],
            'health_facility_code' => ['sometimes', 'string'],
            'DMC' => ['sometimes', 'boolean'],
            'TRUNAT' => ['sometimes', 'boolean'],
            'CBNAAT' => ['sometimes', 'boolean'],
            'X_RAY' => ['sometimes', 'boolean'],
            'ICTC' => ['sometimes', 'boolean'],
            'CDST/LPA_Lab' => ['sometimes', 'boolean'],
            'DM_SCREENING/CONFIRMATION_CENTER' => ['sometimes', 'boolean'],
            'Tobacco_Cessation_clinic' => ['sometimes', 'boolean'],
            'ANC_Clinic' => ['sometimes', 'boolean'],
            'Nutritional_Rehabilitation_centre' => ['sometimes', 'boolean'],
            'De_addiction_centres' => ['sometimes', 'boolean'],
            'ART_Centre' => ['sometimes', 'boolean'],
            'District_DRTB_Centre' => ['sometimes', 'boolean'],
            'NODAL_DRTB_CENTER' => ['sometimes', 'boolean'],
            'IRL' => ['sometimes', 'boolean'],
            'Pediatric_Care_Facility' => ['sometimes', 'boolean'],
            'longitude' => ['sometimes', 'numeric'],
            'latitude' => ['sometimes', 'numeric'],

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