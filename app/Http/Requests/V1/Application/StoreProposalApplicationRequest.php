<?php

namespace App\Http\Requests\V1\Application;

use App\Rules\UserIssetDatabase;
use Illuminate\Foundation\Http\FormRequest;

class StoreProposalApplicationRequest extends FormRequest
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
            'proposal_name'       => 'required|string',
            'application_type'    => 'required|digits_between:1,7|numeric',
            'proponent'           => 'required|string|min:4',
            'price_proposal'      => 'required|numeric',
            'account_information' => 'required|string',
            'delivery_time'       => 'required|string',
            'delivery_date'       => 'required|date|after_or_equal:'.date('Y-m-d'),
            'user_id'             => ['required', new UserIssetDatabase()],
            'user_follows'        => 'nullable|array',
            'user_follows.*'      => [new UserIssetDatabase()],
            'files.*'             => 'nullable|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx',
        ];
    }
}
