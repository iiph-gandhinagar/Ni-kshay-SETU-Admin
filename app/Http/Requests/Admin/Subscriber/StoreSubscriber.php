<?php

namespace App\Http\Requests\Admin\Subscriber;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreSubscriber extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.subscriber.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'api_token' => ['required', Rule::unique('subscribers', 'api_token'), 'string'],
            'name' => ['required', 'string'],
            'phone_no' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],
            'cadre_type' => ['required', 'string'],
            'is_verified' => ['required', 'boolean'],
            'cadre_id' => ['required', 'integer'],
            'block_id' => ['required', 'integer'],
            'district_id' => ['required', 'integer'],
            'state_id' => ['required', 'integer'],
            'health_facility_id' => ['required', 'integer'],
            
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