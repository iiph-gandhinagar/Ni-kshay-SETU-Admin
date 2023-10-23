<?php

namespace App\Http\Requests\Admin\DifferentialCareAlgorithm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexDifferentialCareAlgorithm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.differential-care-algorithm.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:activated,cadre_id,description,has_options,header,id,index,is_expandable,master_node_id,node_type,parent_id,redirect_algo_type,redirect_node_id,state_id,sub_header,time_spent,title|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
