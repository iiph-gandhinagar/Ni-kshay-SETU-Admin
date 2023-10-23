<?php

namespace App\Http\Requests\Admin\UserNotification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserNotification extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.user-notification.edit', $this->userNotification);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string'],
            'user_id' => ['sometimes', 'array'],
            'state_id' => ['nullable', 'array'],
            'district_id' => ['nullable', 'array'],
            'cadre_type' => ['nullable', 'string'],
            'cadre_id' => ['nullable', 'array'],
            'is_deeplinking' => ['sometimes', 'boolean'],
            'automatic_notification_type' => ['nullable', 'string'],
            'type_title' => ['nullable', 'string'],

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
