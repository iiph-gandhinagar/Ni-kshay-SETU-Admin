<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BlockResource;
use App\Http\Resources\DistrictResource;
use App\Http\Resources\HealthFacilityResource;

class UserResource extends JsonResource
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
            'api_token' => $this->api_token,
            'name' => $this->name,
            'phone_no' => $this->phone_no,
            'cadre_type' => $this->cadre_type,
            'is_verified' => $this->is_verified,
            'cadre_id' => $this->cadre_id,
            'cadre_title' => $this->cadre->title,
            'block_id' => $this->block_id,
            'block_title' => new BlockResource($this->block),
            'district_id' => $this->district_id,
            'district_title' =>  new DistrictResource($this->district),
            'state_id' => $this->state_id,
            'state_title' => $this->state->title,
            // 'state_title' => new StateResource($this->state),
            'health_facility_id' => $this->health_facility_id,
            'health_facility_title' => new HealthFacilityResource($this->health_facilities),
            
        ];
    }
}
