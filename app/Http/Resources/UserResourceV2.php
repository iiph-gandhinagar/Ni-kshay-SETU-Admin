<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Subscriber;

class UserResourcev2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $subscriber = Subscriber::with(['media'])->where('id', $this->id)->get()[0];
        // $subscriber->getMedia('profile_image')[0]->hasGeneratedConversion('thumb_60');
        // Log::info($subscriber['media'][0]->getPath('thumb_60'));
        // Log::info($subscriber['media'][0])
        // Log::info($subscriber['media'][0]->hasGeneratedConversion('thumb_60'));
        // Log::info(isset($subscriber['media'][0]) ? $subscriber['media'][0]->getPath() : []);
        return [
            'id' => $this->id,
            'api_token' => $this->api_token,
            'name' => $this->name,
            'phone_no' => $this->phone_no,
            'cadre_type' => $this->cadre_type,
            'is_verified' => $this->is_verified,
            'cadre_id' => $this->cadre_id,
            'country_id' => $this->country_id,
            'cadre_title' => $this->cadre->title,
            'country_title' =>  isset($this->country->title) ? $this->country->title : null,
            'block_id' => $this->block_id,
            'block_title' => isset($this->block->title) ? $this->block->title : null,
            'district_id' => $this->district_id,
            'district_title' =>  isset($this->district->title) ? $this->district->title : null,
            'state_id' => $this->state_id,
            // 'state_title' => $this->state->title,
            'state_title' => isset($this->state->title) ? $this->state->title : null,
            'health_facility_id' => $this->health_facility_id,
            'health_facility_title' =>  isset($this->health_facility->title) ? $this->health_facility->title : null,
            'level' => $this->level,
            'level_title' => $this->level_title,
            'percentage' => $this->percentage,
            'media' => isset($subscriber['media'][0]) ? [[
                "origin" => $subscriber['media'][0],
                "thumb_60" =>  $subscriber['media'][0]->hasGeneratedConversion('thumb_60') ? $subscriber['media'][0]->getPath('thumb_60') : '',
                "thumb_100" => $subscriber['media'][0]->hasGeneratedConversion('thumb_100') ? $subscriber['media'][0]->getPath('thumb_100') : ''
            ]] : [],
        ];
    }
}
