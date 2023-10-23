<?php

namespace App\Http\Requests\Admin\LbSubscriberRanking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreLbSubscriberRanking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.lb-subscriber-ranking.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subscriber_id' => ['required', 'integer'],
            'level_id' => ['required', 'integer'],
            'badge_id' => ['required', 'integer'],
            'mins_spent_count' => ['required', 'string'],
            'sub_module_usage_count' => ['required', 'string'],
            'App_opended_count' => ['required', 'integer'],
            'chatbot_usage_count' => ['required', 'integer'],
            'resource_material_accessed_count' => ['required', 'integer'],
            'total_task_count' => ['required', 'integer'],

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
