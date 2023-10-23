<?php

namespace App\Http\Requests\Admin\UserFeedbackHistory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserFeedbackHistory extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.user-feedback-history.edit', $this->userFeedbackHistory);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subscriber_id' => ['sometimes', 'integer'],
            'feedback_id' => ['sometimes', 'integer'],
            'ratings' => ['nullable', 'integer'],
            'review' => ['nullable', 'string'],
            'skip' => ['sometimes', 'boolean'],

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
