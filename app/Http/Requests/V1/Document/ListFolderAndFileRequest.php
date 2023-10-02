<?php

namespace App\Http\Requests\V1\Document;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListFolderAndFileRequest extends FormRequest
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
            'status' => ['nullable',
                Rule::in(DocumentStatus::ALL, DocumentStatus::TRASH, DocumentStatus::ALL_PRIVATE,
                    DocumentStatus::SHARE)]
        ];
    }
}
