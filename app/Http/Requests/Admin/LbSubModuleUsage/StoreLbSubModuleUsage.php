<?php

namespace App\Http\Requests\Admin\LbSubModuleUsage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreLbSubModuleUsage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.lb-sub-module-usage.create');
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
            'module_id' => ['required', 'string'],
            'sub_module' => ['required', 'string'],
            'total_time' => ['required', 'string'],
            'mins_spent' => ['required', 'string'],
            'completed_flag' => ['required', 'boolean'],

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
