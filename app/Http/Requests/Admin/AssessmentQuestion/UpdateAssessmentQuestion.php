<?php

namespace App\Http\Requests\Admin\AssessmentQuestion;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAssessmentQuestion extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.assessment-question.edit', $this->assessmentQuestion);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'assessment_id' => ['sometimes', 'integer'],
            'correct_answer' => ['sometimes', 'string'],
            'order_index' => ['sometimes', 'integer'],
            'category' => ['sometimes', 'string'],

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
                'question' => ['required', 'string'],
                'option1' => ['required', 'string'],
                'option2' => ['required', 'string'],
                'option3' => ['required', 'string'],
                'option4' => ['required', 'string'],
            ];
        } else {
            return [
                'question' => ['nullable', 'string'],
                'option1' => ['nullable', 'string'],
                'option2' => ['nullable', 'string'],
                'option3' => ['nullable', 'string'],
                'option4' => ['nullable', 'string'],
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
