<?php

namespace App\Http\Requests\Admin\FlashSimilarApp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreFlashSimilarApp extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.flash-similar-app.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'active' => ['required', 'boolean'],
            'href' => ['required', 'string'],
            'order_index' => ['required', 'integer'],
            'href_web' => ['required', 'string'],
            'href_ios' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'title' => ['required', 'string'],

        ];
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
