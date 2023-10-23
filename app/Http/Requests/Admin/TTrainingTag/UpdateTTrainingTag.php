<?php

namespace App\Http\Requests\Admin\TTrainingTag;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTTrainingTag extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.t-training-tag.edit', $this->tTrainingTag);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'tag' => ['sometimes', 'string'],
            'pattern' => ['nullable', 'array'],
            'is_fix_response' => ['sometimes', 'boolean'],
            // 'like_count' => ['sometimes', 'integer'],
            // 'dislike_count' => ['sometimes', 'integer'],
            'questions' => ['nullable', 'array'],
            'modules' => ['nullable', 'array'],
            'sub_modules' => ['nullable', 'array'],
            'resource_material' => ['nullable', 'array'],

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
                'response' => ['required_if:is_fix_response,1'],
            ];
        } else {
            return [
                'response' => ['nullable', 'array'],
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
