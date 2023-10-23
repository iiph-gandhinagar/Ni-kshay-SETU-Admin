<?php

namespace App\Http\Requests\Admin\HealthFacility;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexHealthFacility extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.health-facility.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:id,state_id,district_id,block_id,health_facility_code,DMC,TRUNAT,CBNAAT,X_RAY,ICTC,CDST/LPA_Lab,DM_SCREENING/CONFIRMATION_CENTER,Tobacco_Cessation_clinic,ANC_Clinic,Nutritional_Rehabilitation_centre,De_addiction_centres,ART_Centre,District_DRTB_Centre,NODAL_DRTB_CENTER,IRL,Pediatric_Care_Facility,longitude,latitude,created_at|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
