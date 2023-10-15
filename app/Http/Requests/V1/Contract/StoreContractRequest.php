<?php

namespace App\Http\Requests\V1\Contract;

use App\Enums\ContractStatus;
use App\Rules\ContractByIdBankingCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractRequest extends FormRequest
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
            'created_contract' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            'code_fax' => 'nullable|numeric',
            'name_customer' => 'required|string',
            'email' => 'required|email:rfc,dns',
            'name_cty' => 'required|string',
            'address' => 'required|string',
            'name_account' => 'nullable|numeric',
            'banking_id' => [new ContractByIdBankingCheck(), 'nullable'],
            'payments' => 'nullable|in:1,2',
            'files' => 'required|array',
            'files.*' => 'required|mimes:pdf',
            'signatures' => 'required|array',
            'signatures.*.name' => 'required|string',
            'signatures.*.sign_sequence' => 'required|numeric|between:1,5|distinct',
            'signatures.*.phone' => 'required|string|size:10',
            'signatures.*.email' => 'required|email:rfc,dns',
            'signatures_follow' => 'required|array',
            'signatures_follow.*.business_name_follow' => 'required|string',
            'signatures_follow.*.phone_follow' => 'required|string|size:10',
            'signatures_follow.*.email_follow' => 'required|email:rfc,dns',
            'type' => ['required', Rule::in(ContractStatus::COMPANY, ContractStatus::PERSONAL)]
        ];
    }
}
