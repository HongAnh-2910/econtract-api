<?php

namespace App\Http\Requests\V1\Department;

use App\Enums\TypeDelete;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestroyTypeDepartmentRequest extends FormRequest
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
            'type' => ['required',Rule::in(TypeDelete::SOFT_DELETE , TypeDelete::DELETE)]
        ];
    }
}
