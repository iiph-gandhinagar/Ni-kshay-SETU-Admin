<?php

namespace App\Http\Requests\Admin\StaticFaq;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateStaticFaq extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.static-faq.edit', $this->staticFaq);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'active' => ['sometimes', 'boolean'],
            'order_index' => ['sometimes', 'integer'],


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
                'description' => ['required', 'string'],
                'question' => ['required', 'string'],

            ];
        } else {
            return [
                'description' => ['nullable', 'string'],
                'question' => ['nullable', 'string'],

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
