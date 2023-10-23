<?php

namespace App\Http\Requests\Admin\HealthFacility;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreHealthFacility extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.health-facility.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'country_id' => ['required', 'array'],
            'state_id' => ['required', 'array'],
            'district_id' => ['required', 'array'],
            'block_id' => ['required', 'array'],
            'health_facility_code' => ['required', 'string'],
            'DMC' => ['required', 'boolean'],
            'TRUNAT' => ['required', 'boolean'],
            'CBNAAT' => ['required', 'boolean'],
            'X_RAY' => ['required', 'boolean'],
            'ICTC' => ['required', 'boolean'],
            'LPA_Lab' => ['required', 'boolean'],
            'CONFIRMATION_CENTER' => ['required', 'boolean'],
            'Tobacco_Cessation_clinic' => ['required', 'boolean'],
            'ANC_Clinic' => ['required', 'boolean'],
            'Nutritional_Rehabilitation_centre' => ['required', 'boolean'],
            'De_addiction_centres' => ['required', 'boolean'],
            'ART_Centre' => ['required', 'boolean'],
            'District_DRTB_Centre' => ['required', 'boolean'],
            'NODAL_DRTB_CENTER' => ['required', 'boolean'],
            'IRL' => ['required', 'boolean'],
            'Pediatric_Care_Facility' => ['required', 'boolean'],
            'longitude' => ['required', 'numeric'],
            'latitude' => ['required', 'numeric'],

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
