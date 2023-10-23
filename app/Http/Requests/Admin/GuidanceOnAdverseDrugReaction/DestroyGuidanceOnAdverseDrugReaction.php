<?php

namespace App\Http\Requests\Admin\GuidanceOnAdverseDrugReaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DestroyGuidanceOnAdverseDrugReaction extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.guidance-on-adverse-drug-reaction.delete', $this->guidanceOnAdverseDrugReaction);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
