<?php

namespace App\Http\Requests\Admin\ChatbotActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreChatbotActivity extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.chatbot-activity.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'action' => ['required', 'string'],
            'payload' => ['nullable', 'string'],
            'plateform' => ['required', 'string'],
            'ip_address' => ['nullable', 'string'],
            'tag_id' => ['nullable', 'integer'],
            'question_id' => ['nullable', 'integer'],
            'like' => ['nullable', 'integer'],
            'dislike' => ['nullable', 'integer'],

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
