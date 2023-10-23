<?php

namespace App\Http\Requests\Admin\Subscriber;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateSubscriber extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.subscriber.edit', $this->subscriber);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'api_token' => ['sometimes', Rule::unique('subscribers', 'api_token')->ignore($this->subscriber->getKey(), $this->subscriber->getKeyName()), 'string'],
            'name' => ['sometimes', 'string'],
            'phone_no' => ['sometimes', 'string'],
            'password' => ['sometimes', 'confirmed', 'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],
            'cadre_type' => ['sometimes', 'string'],
            'is_verified' => ['sometimes', 'boolean'],
            'cadre_id' => ['required', 'array'],
            'country_id' => ['sometimes', 'array'],
            'state_id' => ['sometimes', 'array'],
            'district_id' => ['required_if:cadre_type,"District_Level","Block_Level","Health-facility_Level"', 'array'],
            'block_id' => ['required_if:cadre_type,"Block_Level","Health-facility_Level"', 'array'],
            'health_facility_id' => ['required_if:cadre_type,"Health-facility_Level"', 'array'],
            
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