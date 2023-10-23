<?php

namespace App\Http\Requests\Admin\UserNotification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUserNotification extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.user-notification.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string'],
            'user_id' => ['required_if:type,user-specific', 'array'],
            'country_id' => ['nullable', 'array'],
            'state_id' => ['required_if:type,multiple-filters', 'nullable'],
            'district_id' => ['nullable'],
            'cadre_type' => ['required_if:type,multiple-filters', 'nullable', 'string'],
            'cadre_id' => ['required_if:type,multiple-filters', 'nullable'],
            'is_deeplinking' => ['required', 'boolean'],
            'automatic_notification_type' => ['nullable', 'string'],
            'type_title' => ['nullable', 'array'],

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
