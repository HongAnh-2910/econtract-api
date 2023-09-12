<?php

namespace App\Http\Requests\V1\Auth;
use App\Enums\StatusIsActive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:6',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => 'required|min:6|confirmed',
            'department_id' => 'nullable|array',
            'active' => array('required', Rule::in(StatusIsActive::ACTIVE, StatusIsActive::NOT_ACTIVE))
        ];
    }
}
