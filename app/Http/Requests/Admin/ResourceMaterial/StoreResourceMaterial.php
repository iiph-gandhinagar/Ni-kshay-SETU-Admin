<?php

namespace App\Http\Requests\Admin\ResourceMaterial;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;
use App\Rules\FileTypeCheck;
use Illuminate\Http\Request;

class StoreResourceMaterial extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        $this->request = $request->all();
        return Gate::allows('admin.resource-material.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        // Log::info($this->request);
        return [
            // 'title' => ['required', 'string'],
            'type_of_materials' => ['required', 'string'],
            'material' => ['required_if:type_of_materials,pdfs,videos,ppt,document,images,pdf_office_orders', new FileTypeCheck($this->request)],
            // 'video_thumb' => ['required_if:type_of_materials,videos'],
            'state' => ['required', 'array'],
            'cadre' => ['required', 'array'],
            'country_id' => ['nullable', 'array'],
            'parent_id' => ['required', 'integer'],
            'icon_type' => ['required_if:parent_id,"0"'],
            'index' => ['required', 'integer'],
            // 'created_by' => ['required', 'integer'],
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
