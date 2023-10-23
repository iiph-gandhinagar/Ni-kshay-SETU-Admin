<?php

namespace App\Http\Requests\Admin\SurveyMasterQuestion;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreSurveyMasterQuestion extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.survey-master-question.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'active' => ['required', 'boolean'],
            'order_index' => ['required', 'integer'],
            'survey_master_id' => ['required', 'array'],
            'type' => ['required', 'string'],

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
                'option1' => ['nullable', 'string'],
                'option2' => ['nullable', 'string'],
                'option3' => ['nullable', 'string'],
                'option4' => ['nullable', 'string'],
                'question' => ['required', 'string'],

            ];
        } else {
            return [
                'option1' => ['nullable', 'string'],
                'option2' => ['nullable', 'string'],
                'option3' => ['nullable', 'string'],
                'option4' => ['nullable', 'string'],
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
