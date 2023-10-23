<?php

namespace App\Http\Requests\Admin\DifferentialCareAlgorithm;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreDifferentialCareAlgorithm extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.differential-care-algorithm.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'node_type' => ['required', 'string'],
            'is_expandable' => ['required', 'boolean'],
            'has_options' => ['required', 'boolean'],
            'parent_id' => ['required', 'integer'],
            'time_spent' => ['nullable', 'string'],
            'index' => ['required', 'integer'],
            'redirect_algo_type' => ['nullable', 'string'],
            // 'redirect_node_id' => ['nullable', 'integer'],
            'activated' => ['required', 'boolean'],
            'master_node_id' => ['required', 'integer'],
            'state_id' => ['nullable', 'array'],
            'cadre_id' => ['nullable', 'array'],

        ];
    }

    /**
     * Get the validation rules that apply to the requests translatable fields.
     *
     * @return array
     */
    public function translatableRules($locale): array
    {
        if ($locale == 'en') {

            return [
                'title' => ['required', 'string'],
                'description' => ['nullable', 'string'],
                'header' => ['nullable', 'string'],
                'sub_header' => ['nullable', 'string'],
            ];
        } else {
            return [
                'title' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
                'header' => ['nullable', 'string'],
                'sub_header' => ['nullable', 'string'],
            ];
        }
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
