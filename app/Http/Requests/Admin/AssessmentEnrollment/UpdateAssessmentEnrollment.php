<?php

namespace App\Http\Requests\Admin\AssessmentEnrollment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAssessmentEnrollment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.assessment-enrollment.edit', $this->assessmentEnrollment);
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
            'response' => ['nullable', 'string'],
            'send_inital_invitation' => ['sometimes', 'boolean'],

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
