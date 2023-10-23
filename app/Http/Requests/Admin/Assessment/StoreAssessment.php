<?php

namespace App\Http\Requests\Admin\Assessment;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreAssessment extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.assessment.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'time_to_complete' => ['required', 'integer'],
            'country_id' => ['nullable', 'array'],
            'cadre_id' => ['required', 'array'],
            'state_id' => ['required', 'array'],
            'assessment_type' => ['nullable', 'string'],
            'from_date' => ['nullable', 'string'],
            'to_date' => ['nullable', 'string'],
            'initial_invitation' => ['required', 'boolean'],
            'activated' => ['required', 'boolean'],
            'district_id' => ['required', 'array'],
            'cadre_type' => ['nullable', 'string'],
            // 'created_by' => ['required', 'integer'],
            'certificate_type' => ['required', 'array'],
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
