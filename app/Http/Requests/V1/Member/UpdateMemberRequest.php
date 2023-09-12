<?php

namespace App\Http\Requests\V1\Member;

use App\Enums\StatusIsActive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
            'name'          => 'required|min:6',
            'email'         => ['required',
                'email:rfc,dns',
                Rule::unique('users')->ignore($this->user->id)],
            'password'      => 'nullable|min:6|confirmed',
            'department_id' => 'required|array',
            'images'        => 'nullable|mimes:jpeg,png,jpg,gif|image',
            'active'        => array('required', Rule::in(StatusIsActive::ACTIVE, StatusIsActive::NOT_ACTIVE))
        ];
    }
}
