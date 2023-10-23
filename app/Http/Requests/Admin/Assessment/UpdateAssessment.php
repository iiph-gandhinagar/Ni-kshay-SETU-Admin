<?php

namespace App\Http\Requests\Admin\Assessment;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAssessment extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.assessment.edit', $this->assessment);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            // 'assessment_title' => ['sometimes', 'string'],
            'time_to_complete' => ['sometimes', 'integer'],
            'cadre_id' => ['sometimes', 'array'],
            // 'country_id' => ['nullable','array'],
            'state_id' => ['sometimes', 'array'],
            'assessment_type' => ['nullable', 'string'],
            'from_date' => ['nullable', 'string'],
            'to_date' => ['nullable', 'string'],
            'initial_invitation' => ['sometimes', 'boolean'],
            'activated' => ['sometimes', 'boolean'],
            'district_id' => ['sometimes', 'array'],
            'cadre_type' => ['nullable', 'string'],
            // 'created_by' => ['sometimes', 'integer'],
            'certificate_type' => ['sometimes', 'array'],
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
                'assessment_title' => ['required', 'string'],
            ];
        } else {
            return [
                'assessment_title' => ['nullable', 'string'],
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
