<?php

namespace App\Http\Requests\Admin\StaticBlog;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreStaticBlog extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.static-blog.create');
    }

/**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array {
        return [
            'active' => ['required', 'boolean'],
            'author' => ['required', 'string'],
            'keywords' => ['required', 'array'],
            'order_index' => ['required', 'integer'],
            'slug' => ['required', Rule::unique('static_blogs', 'slug'), 'string'],
            'source' => ['required', 'string'],
            'blog_thumb_image1' => ['required'],//,'dimensions:1241,443'
            'blog_thumb_image2' => ['required'],//,'dimensions:591,261'
            'blog_thumb_image3' => ['required'],//,'dimensions:713,445'
            
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
                'description' => ['required', 'string'],
                'short_description' => ['nullable', 'string'],
                'title' => ['required', 'string'],
                
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