<?php

namespace App\Http\Requests\Admin\UserAssessment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUserAssessment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.user-assessment.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'assessment_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'total_marks' => ['required', 'integer'],
            'obtained_marks' => ['required', 'integer'],
            'attempted' => ['required', 'integer'],
            'right_answers' => ['required', 'integer'],
            'wrong_answers' => ['required', 'integer'],
            'skipped' => ['required', 'integer'],

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
