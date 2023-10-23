<?php

namespace App\Http\Requests\Admin\AutomaticNotification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAutomaticNotification extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.automatic-notification.edit', $this->automaticNotification);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'string'],
            'linking_url' => ['sometimes', 'string'],
            'subscriber_id' => ['sometimes', 'string'],
            'title' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string'],

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
