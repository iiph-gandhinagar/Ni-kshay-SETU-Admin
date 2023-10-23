<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'state_id' => $this->state_id,
            'state_title' => $this->state->title,
            'district_id' => $this->district_id,
            'district_title' => $this->district->title,
            'block_id' => $this->block_id,
            'block_title' => $this->block->title,
            'health_facility_code' => $this->health_facility_code,
            'DMC' => $this->DMC,
            'TRUNAT' => $this->TRUNAT,
            'CBNAAT' => $this->CBNAAT,
            'X_RAY' => $this->X_RAY,
            'ICTC' => $this->ICTC,
            'LPA_Lab' => $this->LPA_Lab,
            'CONFIRMATION_CENTER' => $this->CONFIRMATION_CENTER,
            'Tobacco_Cessation_clinic' => $this->Tobacco_Cessation_clinic,
            'ANC_Clinic' => $this->ANC_Clinic,
            'Nutritional_Rehabilitation_centre' => $this->Nutritional_Rehabilitation_centre,
            'De_addiction_centres' => $this->De_addiction_centres,
            'ART_Centre' => $this->ART_Centre,
            'District_DRTB_Centre' => $this->District_DRTB_Centre,
            'NODAL_DRTB_CENTER' => $this->NODAL_DRTB_CENTER,
            'IRL' => $this->IRL,
            'Pediatric_Care_Facility' => $this->Pediatric_Care_Facility,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ];
    }
}
