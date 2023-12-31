<?php

namespace App\Http\Requests\Admin\AdminUser;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Validations\StateRoleValidations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class StoreAdminUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('admin.admin-user.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request): array
    {
        $rules = [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:admin_users,email,NULL,id,deleted_at,NULL', 'string'],
            'password' => ['required', 'confirmed', 'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],
            'forbidden' => ['required', 'boolean'],
            'language' => ['required', 'string'],
            'roles' => ['required'],
            'state' => ['required', new StateRoleValidations($request)],
            'district' => ['required'],
            'cadre' => ['required'],
            'country' => ['nullable'],
            'role_type' => ['required'],
        ];

        if (Config::get('admin-auth.activation_enabled')) {
            $rules['activated'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * Modify input data
     *
     * @return array
     */
    public function getModifiedData(Request $request): array
    {
        $data = $this->only(collect($this->rules($request))->keys()->all());
        if (!Config::get('admin-auth.activation_enabled')) {
            $data['activated'] = true;
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $data;
    }
}
