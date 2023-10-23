<?php

namespace App\Http\Requests\Admin\StaticResourceMaterial;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use App\Rules\FileTypeCheck;
use Illuminate\Http\Request;

class StoreStaticResourceMaterial extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        $this->request = $request->all();
        return Gate::allows('admin.static-resource-material.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'active' => ['required', 'boolean'],
            'order_index' => ['required', 'integer'],
            'type_of_materials' => ['required', 'string'],
            'material' => ['required_if:type_of_materials,pdfs,videos,ppt,document,images,pdf_office_orders', new FileTypeCheck($this->request)],
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
