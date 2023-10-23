<?php

namespace App\Http\Validations;

use Illuminate\Contracts\Validation\Rule;

class StateRoleValidations implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $request;
    protected $err;
    public function __construct($request)
    {
        $this->request = $request;
        $this->err = "";
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $request = $this->request;
        if (isset($request['id']) && $request['id'] > 0) {
            if ($request->input('roles') && isset($request->input('roles')['id'])) {
                $roleId = collect($request->input('roles', []))['id'];
            } else {
                $roleId = collect($request->input('roles', []))[0]['id'];
            }
        } else {
            $roleId = collect($request->input('roles', []))['id'];
        }
        if (($roleId == 3 || $roleId == 4) && ($request['state'] == null || $request['state'] == '')) {
            $this->err = 'State is required when Role is State Admin or State View Only!';
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->err;
    }
}
