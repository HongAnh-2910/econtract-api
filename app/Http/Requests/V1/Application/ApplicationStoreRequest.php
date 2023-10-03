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
            'reason'                        => 'required|string',
            'application_type'              => 'required|digits_between:1,7|numeric',
            'date_rest'                     => 'required|array',
            'date_rest.*.information_day_2' => 'required|date|after_or_equal:'.date('Y-m-d'),
            'date_rest.*.information_day_4' => 'required|date|after_or_equal:date_rest.*.information_day_2',
            'des'                           => 'required|string',
            'user_id'                       => ['required', new UserIssetDatabase()],
            'user_follows'                  => 'nullable|array',
            'user_follows.*'                => [new UserIssetDatabase()],
            'files.*'                         => 'nullable|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx',
        ];
    }
}
