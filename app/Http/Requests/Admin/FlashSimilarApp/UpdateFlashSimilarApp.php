<?php

namespace App\Http\Requests\Admin\FlashSimilarApp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateFlashSimilarApp extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.flash-similar-app.edit', $this->flashSimilarApp);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'active' => ['sometimes', 'boolean'],
            'href' => ['sometimes', 'string'],
            'href_web' => ['sometimes', 'string'],
            'href_ios' => ['sometimes', 'string'],
            'order_index' => ['sometimes', 'integer'],
            'sub_title' => ['sometimes', 'string'],
            'title' => ['sometimes', 'string'],


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
