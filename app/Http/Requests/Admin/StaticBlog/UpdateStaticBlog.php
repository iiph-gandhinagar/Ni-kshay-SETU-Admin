<?php

namespace App\Http\Requests\Admin\StaticBlog;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateStaticBlog extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.static-blog.edit', $this->staticBlog);
    }

/**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array {
        return [
            'active' => ['sometimes', 'boolean'],
            'author' => ['sometimes', 'string'],
            'keywords' => ['sometimes', 'array'],
            'order_index' => ['sometimes', 'integer'],
            'slug' => ['sometimes', Rule::unique('static_blogs', 'slug')->ignore($this->staticBlog->getKey(), $this->staticBlog->getKeyName()), 'string'],
            'source' => ['sometimes', 'string'],
            

        ];
    }

    /**
     * Get the validation rules that apply to the requests translatable fields.
     *
     * @return array
     */
    public function translatableRules($locale): array {
        if($locale == 'en'){
            return [
                'description' => ['sometimes', 'string'],
                'short_description' => ['nullable', 'string'],
                'title' => ['sometimes', 'string'],
                
            ];
        }else{
            return [
                'description' => ['nullable', 'string'],
                'short_description' => ['nullable', 'string'],
                'title' => ['nullable', 'string'],
                
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