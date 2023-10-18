<?php

namespace App\Http\Requests\V1\Contract;

use App\Models\Signature;
use App\Rules\SignatureByIdCheck;
use Illuminate\Foundation\Http\FormRequest;

class SetupSignatureSuccessRequest extends FormRequest
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
            'signatures' =>'required|array',
            'signatures.*.signature_id' => ['required',new SignatureByIdCheck(new Signature()) ,'required'],
            'signatures.*.dataX' => 'required|numeric',
            'signatures.*.dataY' => 'required|numeric',
            'signatures.*.dataPage' => 'required|numeric',
            'signatures.*.type' => 'required|in:1,2,3,4,5',
            'signatures.*.width' => 'required|numeric',
            'signatures.*.height' => 'required|numeric',
        ];
    }
}
