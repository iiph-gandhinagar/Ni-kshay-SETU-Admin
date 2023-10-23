<?php

namespace App\Http\Requests\Admin\SurveyMaster;

use Brackets\Translatable\TranslatableFormRequest;
use Illuminate\Support\Facades\Gate;

class StoreSurveyMaster extends TranslatableFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.survey-master.create');
    }

    /**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * @return array
     */
    public function untranslatableRules(): array
    {
        return [
            'active' => ['required', 'boolean'],
            'country_id' => ['nullable', 'array'],
            'cadre_id' => ['required', 'array'],
            'state_id' => ['required', 'array'],
            'district_id' => ['nullable', 'array'],
            'cadre_type' => ['required', 'string'],
            'order_index' => ['required', 'integer'],

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
