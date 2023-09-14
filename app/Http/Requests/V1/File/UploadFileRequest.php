<?php

namespace App\Http\Requests\V1\File;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
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
            'files.*'         => 'required|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx',
            'user_share_ids'  => 'array'
        ];
    }
}
