<?php

namespace App\Http\Requests\Admin\ResourceMaterial;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use App\Rules\FileTypeCheck;
use Illuminate\Http\Request;

class UpdateResourceMaterial extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        $this->request = $request->all();
        return Gate::allows('admin.resource-material.edit', $this->resourceMaterial);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            // 'title' => ['sometimes', 'string'],
            'type_of_materials' => ['sometimes', 'string'],
            'material' => ['sometimes', new FileTypeCheck($this->request)],
            'state' => ['sometimes', 'array'],
            'country_id' => ['nullable'],
            'cadre' => ['sometimes', 'array'],
            'parent_id' => ['sometimes', 'integer'],
            'icon_type' => ['required_if:parent_id,"0"'],
            'index' => ['sometimes', 'integer'],
            // 'created_by' => ['sometimes', 'integer'],


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
