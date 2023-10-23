<?php

namespace App\Http\Requests\Admin\StaticWhatWeDo;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreStaticWhatWeDo extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.static-what-we-do.create');
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
                'location' => ['required', 'string'],
                'title' => ['required', 'string'],
            ];
        } else {
            return [
                'location' => ['nullable', 'string'],
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
