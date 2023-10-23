<?php

namespace App\Http\Requests\Admin\LbSubModuleUsage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLbSubModuleUsage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.lb-sub-module-usage.edit', $this->lbSubModuleUsage);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subscriber_id' => ['sometimes', 'integer'],
            'module_id' => ['sometimes', 'string'],
            'sub_module' => ['sometimes', 'string'],
            'total_time' => ['sometimes', 'string'],
            'mins_spent' => ['sometimes', 'string'],
            'completed_flag' => ['sometimes', 'boolean'],

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
