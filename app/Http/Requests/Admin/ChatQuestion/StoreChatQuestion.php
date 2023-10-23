<?php

namespace App\Http\Requests\Admin\ChatQuestion;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreChatQuestion extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.chat-question.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            // 'question' => ['required', 'string', 'unique:chat_questions,question'],
            // 'answer' => ['required', 'string'],
            'keyword_id' => ['required', 'array'],
            // 'hit' => ['required', 'string'],
            'cadre_id' => ['required', 'array'],
            'category' => ['nullable', 'string'],
            'activated' => ['required', 'boolean'],
            // 'like_count' => ['nullable', 'integer'],
            // 'dislike_count' => ['nullable', 'integer'],

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
