<?php

namespace App\Http\Requests\V1\Application;

use App\Rules\UserIssetDatabase;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationStoreRequest extends FormRequest
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
            'reason' => 'required|string',
            'application_type' => 'required|string',
            'date_rest' => 'required|array',
            'date_rest.*.date_from' => 'required|date',
            'date_rest.*.date_to' => 'required|date|after_or_equal:date_rest.*.date_from',
            'des' => 'required|string',
            'user_id' => ['required' , new UserIssetDatabase()],
            'user_follows' => 'nullable|array',
            'user_follows.*' => [new UserIssetDatabase()],
            'files' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx',
        ];
    }
}
