<?php

namespace App\Http\Requests\Admin\StaticResourceMaterial;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use App\Rules\FileTypeCheck;
use Illuminate\Http\Request;

class UpdateStaticResourceMaterial extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        $this->request = $request->all();
        return Gate::allows('admin.static-resource-material.edit', $this->staticResourceMaterial);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'active' => ['sometimes', 'boolean'],
            'order_index' => ['sometimes', 'integer'],
            'type_of_materials' => ['sometimes', 'string'],
            'material' => ['sometimes', new FileTypeCheck($this->request)],

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
                'title' => ['required', 'string'],

            ];
        } else {
            return [
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
