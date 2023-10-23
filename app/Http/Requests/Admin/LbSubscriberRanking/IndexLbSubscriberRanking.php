<?php

namespace App\Http\Requests\Admin\LbSubscriberRanking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexLbSubscriberRanking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.lb-subscriber-ranking.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:App_opended_count,badge_id,chatbot_usage_count,id,level_id,mins_spent_count,resource_material_accessed_count,sub_module_usage_count,subscriber_id,total_task_count,created_at|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
