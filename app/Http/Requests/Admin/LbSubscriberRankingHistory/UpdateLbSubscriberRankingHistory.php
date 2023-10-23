<?php

namespace App\Http\Requests\Admin\LbSubscriberRankingHistory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLbSubscriberRankingHistory extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.lb-subscriber-ranking-history.edit', $this->lbSubscriberRankingHistory);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'lb_subscriber_rankings_id' => ['sometimes', 'integer'],
            'subscriber_id' => ['sometimes', 'integer'],
            'level_id' => ['sometimes', 'integer'],
            'badge_id' => ['sometimes', 'integer'],
            'mins_spent_count' => ['sometimes', 'string'],
            'sub_module_usage_count' => ['sometimes', 'string'],
            'App_opended_count' => ['sometimes', 'integer'],
            'chatbot_usage_count' => ['sometimes', 'integer'],
            'resource_material_accessed_count' => ['sometimes', 'integer'],

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
