<?php

namespace App\Http\Requests\Admin\ChatQuestion;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateChatQuestion extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.chat-question.edit', $this->chatQuestion);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            // 'question' => ['sometimes', 'string', 'unique:chat_questions,question,'.$this->chatQuestion->id.',id,deleted_at,NULL'],
            // 'answer' => ['sometimes', 'string'],
            'keyword_id' => ['sometimes', 'array'],
            // 'hit' => ['sometimes', 'string'],
            'cadre_id' => ['sometimes', 'array'],
            'category' => ['nullable', 'string'],
            'activated' => ['sometimes', 'boolean'],
            // 'like_count' => ['sometimes', 'integer'],
            // 'dislike_count' => ['sometimes', 'integer'],


        ];
    }

    /**
     * Get the validation rules that apply to the requests translatable fields.
     *
     * @return array
     */
    public function translatableRules($locale): array
    {
        return [
            'question' => ['nullable', 'string'],
            'answer' => ['nullable', 'string'],
            'training_question1' => ['nullable', 'string'],
            'training_question2' => ['nullable', 'string'],
            'training_question3' => ['nullable', 'string'],
            'training_question4' => ['nullable', 'string'],
            'training_question5' => ['nullable', 'string'],
            'training_question6' => ['nullable', 'string'],
            'training_question7' => ['nullable', 'string'],
            'training_question8' => ['nullable', 'string'],
            'training_question9' => ['nullable', 'string'],
            'training_question10' => ['nullable', 'string'],
            'modules' => ['nullable', 'string'],
            'sub_modules' => ['nullable', 'array'],

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
