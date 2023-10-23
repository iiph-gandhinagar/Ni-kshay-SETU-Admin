<?php

namespace App\Http\Requests\Admin\FlashNews;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreFlashNews extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.flash-news.create');
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
            'author' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'href' => ['nullable', 'string'],
            'order_index' => ['required', 'integer'],
            'publish_date' => ['nullable', 'string'],
            'source' => ['required', 'string'],

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
