<?php

namespace App\Http\Requests\Admin\CgcIntervention;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCgcIntervention extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.cgc-intervention.edit', $this->cgcIntervention);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'chapter_title' => ['sometimes', 'string'],
            'video_title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'reference_title' => ['sometimes', 'string'],
            'video_image' => ['sometimes'],
            'assessment_id' => ['sometimes', 'integer'],
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
