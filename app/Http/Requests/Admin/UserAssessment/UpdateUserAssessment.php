<?php

namespace App\Http\Requests\Admin\UserAssessment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserAssessment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.user-assessment.edit', $this->userAssessment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'assessment_id' => ['sometimes', 'integer'],
            'user_id' => ['sometimes', 'integer'],
            'total_marks' => ['sometimes', 'integer'],
            'obtained_marks' => ['sometimes', 'integer'],
            'attempted' => ['sometimes', 'integer'],
            'right_answers' => ['sometimes', 'integer'],
            'wrong_answers' => ['sometimes', 'integer'],
            'skipped' => ['sometimes', 'integer'],

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
