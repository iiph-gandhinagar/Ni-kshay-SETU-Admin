<?php

namespace App\Http\Requests\Admin\AssessmentCertificate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreAssessmentCertificate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.assessment-certificate.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string',Rule::unique('assessment_certificates', 'title')],
            'top' => ['nullable', 'integer'],
            'left' => ['nullable', 'integer'],
            'assessment_certificate' => ['required'], //,'dimensions:width=2001px,height=1415px'
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