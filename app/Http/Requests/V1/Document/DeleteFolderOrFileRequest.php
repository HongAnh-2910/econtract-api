<?php

namespace App\Http\Requests\V1\Document;

use App\Enums\TypeDelete;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteFolderOrFileRequest extends FormRequest
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
            'type'            => ['required', Rule::in(TypeDelete::SOFT_DELETE, TypeDelete::DELETE)],
            'folder_file_ids' => 'required|array',
//            'folder_file_ids.*'            => 'required',
//            'folder_file_ids.*.type_check' => ['nullable', Rule::in(TypeDelete::SOFT_DELETE, TypeDelete::DELETE)],
        ];
    }
}
