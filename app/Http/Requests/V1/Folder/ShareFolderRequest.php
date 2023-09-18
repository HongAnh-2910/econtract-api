<?php

namespace App\Http\Requests\V1\Folder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShareFolderRequest extends FormRequest
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
            'user_share_ids'  => 'required|array',
            'type_check'      => ['required' , Rule::in(['folder' ,'file'])]
        ];
    }
}
