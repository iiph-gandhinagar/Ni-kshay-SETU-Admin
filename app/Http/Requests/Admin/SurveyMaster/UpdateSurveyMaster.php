<?php

namespace App\Http\Requests\Admin\SurveyMaster;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSurveyMaster extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.survey-master.edit', $this->surveyMaster);
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'active' => ['sometimes', 'boolean'],
            'country_id' => ['sometimes', 'array'],
            'cadre_id' => ['sometimes', 'array'],
            'state_id' => ['sometimes', 'array'],
            'district_id' => ['nullable', 'array'],
            'cadre_type' => ['nullable', 'string'],
            'order_index' => ['sometimes', 'integer'],


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
                'title' => ['required', 'string'],

            ];
        } else {
            return [
                'title' => ['nullable', 'string'],

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
