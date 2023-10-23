<?php

namespace App\Http\Requests\Admin\ChatKeyword;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateChatKeyword extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.chat-keyword.edit', $this->chatKeyword);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            // 'title' => ['sometimes', 'string',  'unique:chat_keywords,title,'.$this->chatKeyword->id.',id,deleted_at,NULL'],
            // 'hit' => ['sometimes', 'string'],
            'modules' => ['nullable', 'array'],
            'sub_modules' => ['nullable', 'array'],
            'resource_material' => ['nullable', 'array'],
            'custom_ordering' => ['sometimes', 'integer'],

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
                'title' => ['required', 'string',  'unique:chat_keywords,title,' . $this->chatKeyword->id . ',id,deleted_at,NULL'],
            ];
        } else {
            return [
                'title' => ['nullable', 'string',  'unique:chat_keywords,title,' . $this->chatKeyword->id . ',id,deleted_at,NULL'],
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
