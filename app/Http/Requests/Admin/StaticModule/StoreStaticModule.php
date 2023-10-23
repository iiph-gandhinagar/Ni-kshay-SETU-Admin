<?php

namespace App\Http\Requests\Admin\StaticModule;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreStaticModule extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.static-module.create');
    }

/**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array {
        return [
            'active' => ['required', 'boolean'],
            'order_index' => ['required', 'integer'],
            'slug' => ['required', Rule::unique('static_module', 'slug'), 'string'],
            
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
                'title' => ['required', 'string'],
                
            ];
        }else{
            return [
                'description' => ['nullable', 'string'],
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