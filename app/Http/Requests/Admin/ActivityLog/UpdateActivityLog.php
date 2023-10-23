<?php

namespace App\Http\Requests\Admin\ActivityLog;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateActivityLog extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.activity-log.edit', $this->activityLog);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'log_name' => ['nullable', 'string'],
            'description' => ['sometimes', 'string'],
            'subject_type' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'string'],
            'causer_type' => ['nullable', 'string'],
            'causer_id' => ['nullable', 'string'],


        ];
    }

    /**
     * Get the validation rules that apply to the requests translatable fields.
     *
     * @return array
     */
    public function translatableRules($locale): array
    {
        return [
            'properties' => ['nullable', 'string'],

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
