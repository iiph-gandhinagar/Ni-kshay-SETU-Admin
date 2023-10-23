<?php

namespace App\Http\Requests\Admin\UserFeedbackQuestion;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserFeedbackQuestion extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.user-feedback-question.edit', $this->userFeedbackQuestion);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            // 'feedback_value' => ['sometimes', 'string'],
            'feedback_time' => ['nullable', 'string'],
            'feedback_type' => ['sometimes', 'string'],
            'feedback_days' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],


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
                'feedback_question' => ['required', 'string'],
                'feedback_description' => ['nullable', 'string'],
            ];
        } else {
            return [
                'feedback_question' => ['nullable', 'string'],
                'feedback_description' => ['nullable', 'string'],
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
