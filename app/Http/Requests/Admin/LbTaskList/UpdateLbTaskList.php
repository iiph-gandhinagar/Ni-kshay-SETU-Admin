<?php

namespace App\Http\Requests\Admin\LbTaskList;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLbTaskList extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.lb-task-list.edit', $this->lbTaskList);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'level' => ['sometimes', 'array'],
            'badges' => ['sometimes', 'array'],
            'mins_spent' => ['nullable', 'string'],
            'sub_module_usage_count' => ['sometimes', 'string'],
            'App_opended_count' => ['sometimes', 'integer'],
            'chatbot_usage_count' => ['nullable', 'integer'],
            'resource_material_accessed_count' => ['nullable', 'integer'],
            'total_task' => ['sometimes', 'integer'],

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
