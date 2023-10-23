<?php

namespace App\Http\Requests\Admin\CgcIntervention;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCgcIntervention extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.cgc-intervention.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'chapter_title' => ['required', 'string'],
            'video_title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'assessment_id' => ['required', 'integer'],
            'reference_title' => ['required', 'string'],
            'video_image' => ['required'],
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
